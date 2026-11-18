"""
============================================================================
 scanner/escanear.py  ->  EL ESCÁNER DE PUERTOS (Python)
============================================================================
TraffiCop - escáner de puertos.

Uso:   python escanear.py <ip>
Salida: un JSON por stdout con los puertos abiertos y su riesgo.

Solo usa la librería estándar. Prueba varios puertos a la vez usando threads.
Por seguridad, solo acepta IPs privadas (red local) o loopback (127.x.x.x).

CONCEPTOS PARA DEFENDER:
  - PUERTO: un "número de puerta" de un equipo (0 a 65535). Cada servicio
    escucha en uno: la web en el 80/443, SSH en el 22, etc. Si un puerto está
    ABIERTO, hay un programa esperando conexiones ahí -> posible entrada.
  - ¿CÓMO SABEMOS SI ESTÁ ABIERTO? Intentamos una conexión TCP (como cuando el
    navegador se conecta a una página). Si el equipo acepta -> abierto.
    Si rechaza o no responde en 0,5 s -> cerrado/filtrado.
  - HILOS (threads): en vez de probar los 18 puertos uno por uno (hasta 9 s),
    probamos hasta 20 a la vez -> el escaneo tarda ~0,5 s.
  - Este script NO sabe nada de la web ni de la base: solo escanea e imprime
    un JSON. PHP lo ejecuta y lee esa salida (ver ScannerService.php).
============================================================================
"""
import ipaddress                                    # validar IPs y redes
import json                                         # armar la salida en JSON
import socket                                       # conexiones de red (TCP)
import sys                                          # leer argumentos y salir con código
from concurrent.futures import ThreadPoolExecutor   # "pileta" de hilos
from datetime import datetime                       # fecha y hora del escaneo

# Puertos que revisamos: puerto -> (servicio, riesgo)
# Es un diccionario: la clave es el número de puerto y el valor una tupla.
# Elegimos 18 puertos comunes. El riesgo lo definimos así:
#   alto  -> servicios muy atacados o que mandan datos SIN cifrar
#            (ftp, telnet, smb, rdp, vnc...).
#   medio -> seguros si están bien configurados (ssh, bases de datos, correo).
#   bajo  -> normales en muchos equipos (web, dns).
PUERTOS = {
    21: ("ftp", "alto"),          # transferencia de archivos, sin cifrar
    22: ("ssh", "medio"),         # consola remota cifrada
    23: ("telnet", "alto"),       # consola remota SIN cifrar (contraseñas a la vista)
    25: ("smtp", "medio"),        # envío de correo
    53: ("dns", "bajo"),          # traduce nombres a IPs
    80: ("http", "bajo"),         # web sin cifrar
    110: ("pop3", "medio"),       # recibir correo
    135: ("msrpc", "alto"),       # servicios internos de Windows
    139: ("netbios", "alto"),     # compartir archivos (Windows viejo)
    143: ("imap", "medio"),       # recibir correo
    443: ("https", "bajo"),       # web cifrada
    445: ("smb", "alto"),         # compartir archivos Windows (ej: ransomware WannaCry)
    3306: ("mysql", "medio"),     # base de datos MySQL
    3389: ("rdp", "alto"),        # escritorio remoto de Windows
    5432: ("postgresql", "medio"),  # base de datos PostgreSQL
    5900: ("vnc", "alto"),        # escritorio remoto
    8000: ("http-dev", "bajo"),   # servidores web de desarrollo
    8080: ("http-alt", "bajo"),   # web alternativa / proxys
}

# Redes permitidas: las privadas (RFC 1918) y loopback.
# "/8", "/12", "/16" indican cuántos bits fijos tiene la red (notación CIDR):
#   10.0.0.0/8      -> 10.0.0.0 a 10.255.255.255
#   172.16.0.0/12   -> 172.16.0.0 a 172.31.255.255
#   192.168.0.0/16  -> 192.168.0.0 a 192.168.255.255
#   127.0.0.0/8     -> la propia computadora (loopback)
# ¿Por qué limitar? Por ética y legalidad: escanear equipos ajenos de
# Internet sin permiso no corresponde. TraffiCop es para TU red.
REDES_PERMITIDAS = [
    ipaddress.ip_network("10.0.0.0/8"),
    ipaddress.ip_network("172.16.0.0/12"),
    ipaddress.ip_network("192.168.0.0/16"),
    ipaddress.ip_network("127.0.0.0/8"),
]

# Cuánto esperamos la respuesta de cada puerto antes de darlo por cerrado.
TIEMPO_ESPERA = 0.5  # segundos por puerto


def ip_permitida(texto):
    """Devuelve True si el texto es una IPv4 privada o loopback."""
    try:
        # Si el texto no es una IPv4 válida, IPv4Address lanza ValueError.
        ip = ipaddress.IPv4Address(texto)
    except ValueError:
        return False
    # any(...) = True si la IP pertenece a AL MENOS UNA de las redes permitidas.
    return any(ip in red for red in REDES_PERMITIDAS)


def probar_puerto(ip, puerto):
    """Intenta conectarse por TCP. Devuelve el puerto si está abierto, o None."""
    # AF_INET = IPv4 ; SOCK_STREAM = TCP.
    # "with" cierra el socket automáticamente al terminar (no deja conexiones abiertas).
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.settimeout(TIEMPO_ESPERA)
        # connect_ex devuelve 0 si la conexión funcionó (puerto ABIERTO) o un
        # código de error si no (a diferencia de connect, no lanza excepción).
        if s.connect_ex((ip, puerto)) == 0:
            return puerto
    return None


def escanear(ip):
    """Prueba todos los puertos en paralelo y arma la lista de abiertos."""
    # ThreadPoolExecutor crea hasta 20 hilos que trabajan al mismo tiempo.
    # hilos.map aplica probar_puerto a cada puerto de PUERTOS (recorrer un
    # diccionario da sus claves) y devuelve los resultados EN ORDEN.
    # lambda p: ... = función anónima que fija la ip y recibe el puerto.
    with ThreadPoolExecutor(max_workers=20) as hilos:
        resultados = hilos.map(lambda p: probar_puerto(ip, p), PUERTOS)

    # Nos quedamos solo con los abiertos (los que no dieron None) y les
    # agregamos el nombre del servicio y el riesgo desde el diccionario.
    abiertos = []
    for puerto in resultados:
        if puerto is not None:
            servicio, riesgo = PUERTOS[puerto]   # "desarmar" la tupla
            abiertos.append({"puerto": puerto, "servicio": servicio, "riesgo": riesgo})
    return abiertos


def main():
    # sys.argv = lista de argumentos: [nombre del script, ip].
    # Tiene que haber exactamente 2 elementos.
    if len(sys.argv) != 2:
        print(json.dumps({"ok": False, "error": "Uso: escanear.py <ip>"}))
        return 1   # código de salida distinto de 0 = hubo error

    ip = sys.argv[1].strip()
    # Segundo control de seguridad (PHP ya validó, pero no confiamos ciegamente).
    if not ip_permitida(ip):
        print(json.dumps({"ok": False, "error": "Solo se permiten IPs privadas o loopback"}))
        return 2

    abiertos = escanear(ip)
    # Imprimimos el resultado como JSON: PHP lo lee y lo convierte en array.
    # "ok": True le avisa a PHP que salió todo bien.
    print(json.dumps({
        "ok": True,
        "objetivo": ip,
        "fecha": datetime.now().isoformat(timespec="seconds"),  # ej: 2026-11-18T10:15:02
        "puertos_revisados": len(PUERTOS),                       # 18
        "abiertos": abiertos,
    }))
    return 0   # 0 = todo bien


# Esto se ejecuta solo si corremos el archivo directamente
# (python escanear.py ...), no si otro programa lo importa.
# sys.exit(main()) termina el programa con el código que devolvió main().
if __name__ == "__main__":
    sys.exit(main())
