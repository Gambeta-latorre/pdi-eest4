# 5. Middlewares y vistas

## ¿Qué es un middleware?

Es código que se ejecuta **antes y/o después de cada pedido**, sin importar qué ruta sea. Es como el **guardia de la entrada** de un edificio: todos los que entran pasan por él.

En el proyecto hay cuatro:

| Middleware | Qué hace |
|---|---|
| `LogMiddleware` (nuestro) | Anota cada visita: método, ruta, código de respuesta y cuánto tardó. Lo escribe en la consola y en `logs/app.log`. Ejemplo: `[2026-11-13 13:21:10] POST /escaneos 201 812.4ms` |
| `MethodOverrideMiddleware` (de Slim) | Los formularios HTML solo pueden mandar GET y POST. Para editar (PUT) y borrar (DELETE) mandamos un campo oculto `_METHOD`, y este middleware cambia el método. |
| `BodyParsingMiddleware` (de Slim) | Lee los datos que manda el formulario para que el controlador los tenga en un array. |
| `ErrorMiddleware` (de Slim) | Si algo explota, muestra una página de error en vez de que se caiga todo. |

**¿Qué pasó con el `authMiddleware`?** Protegía las rutas para que solo entraran usuarios logueados. Se quitó junto con el login, por indicación de los docentes.

## Códigos de respuesta HTTP que usamos

| Código | Significa | Cuándo |
|---|---|---|
| 200 | OK | Mostrar una página |
| 201 | Creado | Se guardó un escaneo nuevo |
| 302 | Redirección | Después de editar o borrar, te manda a otra página |
| 400 | Pedido incorrecto | `?limit=abc` |
| 404 | No encontrado | `/escaneos/999` no existe |
| 422 | Datos inválidos | IP pública o notas de más de 255 caracteres |

## Vistas — `src/views/`

**Qué son:** las **pantallas**, archivos PHP con HTML. Solo muestran datos y no tienen lógica. Las dibuja `PhpRenderer` (de Slim).

- `layout.php`: el **molde común**, con el encabezado, el menú y los estilos. Cada vista se dibuja adentro, en el lugar de `$content`. Así no repetimos el menú en cada página.
- `index.php`: la landing.
- `escaneos/index.php` (historial), `create.php` (formulario), `show.php` (resultado), `update.php` (editar notas), `not_found.php` (no existe).

**`e()`** (en `src/helpers.php`): escapa el texto antes de mostrarlo, para que si alguien escribe `<script>` en las notas, se muestre como texto y no se ejecute. Eso evita el **XSS**.

**`explicarRiesgo()`**: devuelve el consejo según el riesgo ("Cerralo si no lo usás…").

## ¿Qué es Slim?

Un **micro-framework** de PHP: una base ya hecha que resuelve lo repetitivo (recibir pedidos, rutas, middlewares, respuestas) para que nosotros escribamos solo la lógica de la app.

## ¿Qué es Composer?

El **gestor de paquetes** de PHP. En `composer.json` decimos qué librerías usamos (Slim, phpdotenv…) y `composer install` las descarga en la carpeta `vendor/`. También define el comando `composer migrate`.
