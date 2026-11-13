"""
TraffiCop - escáner de puertos.

Uso:   python escanear.py <ip>
Salida: un JSON por stdout con los puertos abiertos y su riesgo.

Solo usa la librería estándar. Prueba varios puertos a la vez usando threads.
Por seguridad, solo acepta IPs privadas (red local) o loopback (127.x.x.x).
"""
import ipaddress
import json
import socket
import sys
from concurrent.futures import ThreadPoolExecutor
from datetime import datetime

# Puertos que revisamos: puerto -> (servicio, riesgo)
PUERTOS = {
    21: ("ftp", "alto"),
    22: ("ssh", "medio"),
    23: ("telnet", "alto"),
    25: ("smtp", "medio"),
    53: ("dns", "bajo"),
    80: ("http", "bajo"),
    110: ("pop3", "medio"),
    135: ("msrpc", "alto"),
    139: ("netbios", "alto"),
    143: ("imap", "medio"),
    443: ("https", "bajo"),
    445: ("smb", "alto"),
    3306: ("mysql", "medio"),
    3389: ("rdp", "alto"),
    5432: ("postgresql", "medio"),
    5900: ("vnc", "alto"),
    8000: ("http-dev", "bajo"),
    8080: ("http-alt", "bajo"),
}

# Redes permitidas: las privadas (RFC 1918) y loopback.
REDES_PERMITIDAS = [
    ipaddress.ip_network("10.0.0.0/8"),
    ipaddress.ip_network("172.16.0.0/12"),
    ipaddress.ip_network("192.168.0.0/16"),
    ipaddress.ip_network("127.0.0.0/8"),
]

TIEMPO_ESPERA = 0.5  # segundos por puerto


def ip_permitida(texto):
    """Devuelve True si el texto es una IPv4 privada o loopback."""
    try:
        ip = ipaddress.IPv4Address(texto)
    except ValueError:
        return False
    return any(ip in red for red in REDES_PERMITIDAS)


def probar_puerto(ip, puerto):
    """Intenta conectarse por TCP. Devuelve el puerto si está abierto, o None."""
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        s.settimeout(TIEMPO_ESPERA)
        if s.connect_ex((ip, puerto)) == 0:
            return puerto
    return None


def escanear(ip):
    """Prueba todos los puertos en paralelo y arma la lista de abiertos."""
    with ThreadPoolExecutor(max_workers=20) as hilos:
        resultados = hilos.map(lambda p: probar_puerto(ip, p), PUERTOS)

    abiertos = []
    for puerto in resultados:
        if puerto is not None:
            servicio, riesgo = PUERTOS[puerto]
            abiertos.append({"puerto": puerto, "servicio": servicio, "riesgo": riesgo})
    return abiertos


def main():
    if len(sys.argv) != 2:
        print(json.dumps({"ok": False, "error": "Uso: escanear.py <ip>"}))
        return 1

    ip = sys.argv[1].strip()
    if not ip_permitida(ip):
        print(json.dumps({"ok": False, "error": "Solo se permiten IPs privadas o loopback"}))
        return 2

    abiertos = escanear(ip)
    print(json.dumps({
        "ok": True,
        "objetivo": ip,
        "fecha": datetime.now().isoformat(timespec="seconds"),
        "puertos_revisados": len(PUERTOS),
        "abiertos": abiertos,
    }))
    return 0


if __name__ == "__main__":
    sys.exit(main())
