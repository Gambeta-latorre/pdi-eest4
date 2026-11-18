# 3. El escáner en Python — `scanner/escanear.py`

**Qué es:** un programa chico que recibe una IP, prueba si 18 puertos comunes están abiertos y devuelve el resultado en **JSON** (un texto con formato que PHP sabe leer). Solo usa la librería estándar de Python, sin instalar nada.

## Qué es un puerto

Una IP es como la **dirección de un edificio**, y los puertos son los **departamentos**. Cada programa "atiende" en un puerto: las páginas web en el 80 y el 443, el escritorio remoto en el 3389, etc. Un puerto abierto quiere decir que hay un programa esperando conexiones ahí.

## Sus partes

| Parte | Qué hace |
|---|---|
| `PUERTOS` | Diccionario con los 18 puertos que revisamos: número → (servicio, riesgo). Por ejemplo `23: ("telnet", "alto")`. |
| `REDES_PERMITIDAS` | Las redes privadas (10.x, 172.16–31.x, 192.168.x) y la propia compu (127.x). |
| `ip_permitida(texto)` | Devuelve verdadero solo si la IP es de la red local. **Es la regla de seguridad.** |
| `probar_puerto(ip, puerto)` | Intenta conectarse a ese puerto con un **socket**. Si se conecta, está abierto. Espera como máximo medio segundo. |
| `escanear(ip)` | Prueba todos los puertos **al mismo tiempo** con hilos (`ThreadPoolExecutor`) y arma la lista de los abiertos con su servicio y riesgo. |
| `main()` | Lee la IP que le pasan, la valida, escanea e imprime el JSON. |

## Conceptos que pueden preguntar

- **Socket:** la herramienta para conectarse a otra máquina por la red. Lo usamos para "golpear la puerta" de cada puerto.
- **Hilos (threads):** permiten hacer varias cosas a la vez. En lugar de probar los 18 puertos uno atrás del otro (unos 9 segundos), los prueba juntos y tarda menos de 1 segundo.
- **JSON:** formato de texto para intercambiar datos entre programas. Python lo escribe y PHP lo lee.

## Cómo lo llama PHP — `ScannerService::escanear`

PHP ejecuta el programa con `proc_open`, pasándole los datos como una **lista** `[python, escanear.py, ip]` y **no** como un texto armado. Así no pasa por la consola (la "shell"), y nadie puede colar un comando escribiendo algo como `127.0.0.1; borrar todo`. Después lee lo que imprimió Python y lo convierte de JSON a un array.

## Por qué validamos la IP dos veces (PHP y Python)

Por **seguridad en capas**: si alguien llamara al script directamente, igual estaría protegido. Una herramienta que escanea puertos podría usarse para atacar a otros, por eso **solo** acepta la red local.

## Cómo se clasifica el riesgo

- **Alto:** servicios muy atacados o que mandan datos sin cifrar (23 telnet, 3389 escritorio remoto, 445 carpetas compartidas, 21 ftp, 5900 vnc…).
- **Medio:** seguros si tienen buena contraseña, pero no deberían estar expuestos (22 ssh, 3306 mysql, correo…).
- **Bajo:** normales en muchos equipos (80 y 443 web, 53 dns).
