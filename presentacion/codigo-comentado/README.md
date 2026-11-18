# Código comentado de TraffiCop (para la defensa)

En esta carpeta está **todo el código del MVP** con la misma estructura que el proyecto real, pero con **comentarios en cada archivo** que explican qué hace cada parte y por qué la hicimos así.

> El código es idéntico al del proyecto (`src/`, `public/`, `scanner/`, `scripts/`). Solo se le agregaron comentarios. El que se ejecuta es el original; esta carpeta es para estudiar.

## Por dónde empezar a leer

Seguí el mismo camino que recorre una petición:

| # | Archivo | Qué vas a entender |
|---|---------|--------------------|
| 1 | `public/index.php` | Por dónde entra todo (Front Controller) |
| 2 | `src/bootstrap.php` | Cómo se arma la app: .env, middlewares, inyección de dependencias, rutas |
| 3 | `src/routes/*.routes.php` | Qué URL va a qué método (CRUD con verbos REST) |
| 4 | `src/middlewares/LogMiddleware.php` | Qué es un middleware y cómo registramos cada petición |
| 5 | `src/controllers/EscaneoController.php` | Validaciones y códigos HTTP (200, 201, 302, 400, 404, 422) |
| 6 | `src/services/EscaneoService.php` | Lógica de negocio y transacciones (commit y rollBack) |
| 7 | `src/services/ScannerService.php` | Cómo PHP ejecuta Python y por qué no hay inyección de comandos |
| 8 | `scanner/escanear.py` | Cómo se detecta un puerto abierto (sockets TCP + hilos) |
| 9 | `src/persistence/*.php` | Conexión PDO, patrón Repositorio y prepared statements (SQL Injection) |
| 10 | `src/database/migrations/*.sql` + `scripts/migrate.php` | Tablas, claves foráneas, ON DELETE CASCADE, migraciones |
| 11 | `src/views/*.php` + `src/helpers.php` | Layout, escape con `e()` (XSS) y el "semáforo" de riesgo |

## Las capas en una línea

```
Navegador → index.php → Middlewares → Ruta → Controller → Service → Repository → MySQL
                                                              └→ ScannerService → escanear.py
```

- **Controller**: valida lo que llega y elige la vista o el código HTTP.
- **Service**: coordina el trabajo (escanear y guardar) y maneja las transacciones.
- **Repository**: es la única clase que escribe SQL.
- **Views**: solo muestran datos, siempre escapados con `e()`.

## Seguridad: 5 cosas para nombrar en la defensa

1. **Solo IPs privadas o 127.x**: se valida dos veces, en PHP (`ScannerService::ipPermitida`) y en Python (`ip_permitida`).
2. **Sin inyección de comandos**: `proc_open` recibe un array y no pasa por la shell.
3. **Sin SQL Injection**: todas las consultas son prepared statements (`?`).
4. **Sin XSS**: todo lo que se imprime pasa por `e()` (`htmlspecialchars`).
5. **Contraseñas fuera del código**: van en `.env`, que no se sube a GitHub.

## Sobre `composer.json`

JSON no admite comentarios, por eso está sin tocar. Lo que tiene:

- `require`: las librerías que usamos (PHP 8.2 o más nuevo, Slim 4 como framework, slim/psr7 para petición y respuesta, slim/php-view para las vistas y phpdotenv para leer el `.env`).
- `autoload.psr-4`: `"App\\": "src/"` hace que la clase `App\services\EscaneoService` se cargue sola desde `src/services/EscaneoService.php`.
- `autoload.files`: carga `src/helpers.php` siempre (por eso `e()` funciona en todas las vistas).
- `scripts`: los atajos `composer migrate` (crea la base y las tablas) y `composer start` (levanta el servidor en `localhost:8000`).

Para más explicaciones y preguntas tipo, mirá también la carpeta `../codigo-explicado/`.
