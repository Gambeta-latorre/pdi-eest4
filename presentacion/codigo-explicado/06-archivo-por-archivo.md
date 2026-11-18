# 6. Archivo por archivo (repaso rápido)

Una frase por archivo y por función, en el estilo que piden los profes.

## Entrada y configuración

| Archivo | Qué es |
|---|---|
| `public/index.php` | La puerta de entrada: toda visita pasa por acá, carga la app y la pone a correr. |
| `src/bootstrap.php` | Arma la aplicación: lee el `.env`, agrega los middlewares, crea los objetos y carga las rutas. |
| `src/helpers.php` | Funciones chicas para las vistas: `e()` (escapar texto) y `explicarRiesgo()` (el consejo según el riesgo). |
| `composer.json` | La lista de librerías que usa el proyecto y los comandos (`composer migrate`). |
| `.env` / `.env.example` | La configuración local (datos de MySQL). El `.env` no se sube; el `.example` es el modelo. |

## Rutas

| Archivo | Qué es |
|---|---|
| `src/routes/home.routes.php` | Conecta `/` con la landing. |
| `src/routes/escaneos.routes.php` | Conecta las 7 direcciones de `/escaneos` con las funciones del controlador. |

## Controladores

**`HomeController`**
- `index` → muestra la landing con una lista de puertos de ejemplo (datos *mock*).

**`EscaneoController`**
- `index` → muestra el historial. Si viene `?limit=N`, valida que sea un entero positivo.
- `create` → muestra el formulario para un escaneo nuevo.
- `store` → valida la IP y las notas, manda a escanear y muestra los datos enviados y el resultado.
- `show` → muestra el detalle de un escaneo, o "no encontrado" si no existe.
- `edit` → muestra el formulario para editar las notas.
- `update` → valida y guarda las notas nuevas (máximo 255 caracteres).
- `destroy` → borra un escaneo.
- `noEncontrado` → muestra la vista de error 404.

## Servicios

**`EscaneoService`**
- `enTransaccion` → ejecuta una operación dentro de una transacción: si falla, deshace todo.
- `listar` → pide la lista de escaneos.
- `ver` → busca un escaneo y le agrega sus puertos.
- `crear` → ejecuta el escaneo y guarda el resultado en la base, todo junto.
- `actualizarNotas` → cambia las notas dentro de una transacción.
- `borrar` → borra el escaneo dentro de una transacción.

**`ScannerService`**
- `ipPermitida` → dice si una IP es de la red local (privada o 127.x).
- `escanear` → ejecuta el programa de Python y devuelve su resultado.

## Persistencia

**`Database`**
- `conexion` → abre la conexión a MySQL con los datos del `.env`.

**`EscaneoRepository`** (todas usan consultas preparadas)
- `listar` → trae los escaneos con la cantidad de puertos abiertos, del más nuevo al más viejo.
- `buscar` → trae un escaneo por su id.
- `puertosDe` → trae los puertos de un escaneo.
- `crear` → inserta un escaneo nuevo.
- `agregarPuerto` → inserta un puerto de un escaneo.
- `actualizarNotas` → cambia las notas de un escaneo.
- `borrar` → borra un escaneo (sus puertos se borran solos por el CASCADE).

## Middleware

- `LogMiddleware::process` → anota cada visita (método, ruta, código y tiempo) en la consola y en `logs/app.log`.

## Base de datos

- `src/database/migrations/001…007_*.sql` → cada cambio de la base, en orden.
- `scripts/migrate.php` → aplica las migraciones que faltan y anota cuáles ya corrió.

## Escáner

- `scanner/escanear.py` → recibe una IP de la red local, prueba 18 puertos al mismo tiempo y devuelve los abiertos con su riesgo, en JSON.

## Vistas

- `layout.php` → el molde común (menú y estilos).
- `index.php` → la landing.
- `escaneos/index.php`, `create.php`, `show.php`, `update.php`, `not_found.php` → historial, formulario, resultado, editar notas y "no encontrado".
