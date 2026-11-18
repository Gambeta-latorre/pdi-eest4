# 4. Base de datos, migraciones y transacciones

Usamos **MySQL / MariaDB** (el que viene con XAMPP). PHP se conecta con **PDO**.

## Las tablas

| Tabla | Qué guarda |
|---|---|
| `escaneos` | Cada escaneo: IP (`objetivo`), fecha, notas y el resultado completo en JSON. **Es la entidad principal.** |
| `puertos` | Los puertos abiertos de cada escaneo: número, servicio y riesgo. Cada fila apunta a su escaneo. |
| `usuarios` | Quedó de cuando la app tenía login. Hoy no se usa. |
| `migraciones` | Anota qué migraciones ya se ejecutaron. |

**Relación:** un escaneo tiene **muchos** puertos (uno a muchos). `puertos.escaneo_id` es una **clave foránea** (FK) que apunta a `escaneos.id`.

**ON DELETE CASCADE:** si se borra un escaneo, la base borra **sola** todos sus puertos. Así no quedan puertos "huérfanos".

## Migraciones — `src/database/migrations/` (TP10)

**Qué son:** archivos SQL numerados con **cada cambio** que se le hizo a la base, en orden. Sirven para que cualquiera pueda armar la base igual a la nuestra corriendo un solo comando: `composer migrate`.

| N° | Qué hace |
|---|---|
| 001 | Crea `usuarios` |
| 002 | Crea `escaneos` |
| 003 | Agrega la FK `escaneos → usuarios` (con `ALTER TABLE`) |
| 004 | Crea `puertos`, con FK a `escaneos` y `ON DELETE CASCADE` |
| 005 | Hace el email único en `usuarios` |
| 006 | Agrega la columna `notas` a `escaneos` (modificar una tabla existente) |
| 007 | Hace opcional el usuario del escaneo (porque se quitó el login) |

`scripts/migrate.php` lee los archivos en orden, ejecuta los que **todavía no** se corrieron y los anota en la tabla `migraciones`. Si lo corrés dos veces, la segunda no hace nada.

**¿Por qué no borramos las migraciones viejas al sacar el login?** Porque una migración es como un historial: ya se aplicó en otras bases. Para cambiar algo se agrega una migración nueva (la 007).

## Transacciones (TP11)

**Qué es:** un grupo de operaciones que se hacen **todas o ninguna**.

Ejemplo: al guardar un escaneo con 3 puertos hay 4 inserciones. Si la tercera falla, no queremos un escaneo a medias. Con la transacción:

- **beginTransaction:** "empiezo".
- Si todo sale bien → **commit:** "confirmo todo".
- Si algo falla → **rollBack:** "deshago todo, como si no hubiera pasado nada".

En el código está en `EscaneoService::enTransaccion()`. Recibe una función, la ejecuta entre `beginTransaction` y `commit`, y si hay un error hace `rollBack`. La usan crear, editar notas y borrar.

## Prepared statements (consultas preparadas)

En vez de pegar los datos del usuario adentro del SQL, se escribe la consulta con **huecos** (`?`) y los datos se mandan aparte:

```php
$stmt = $pdo->prepare('SELECT * FROM escaneos WHERE id = ?');
$stmt->execute([$id]);
```

**Para qué:** evita la **inyección SQL**, que es cuando alguien escribe código SQL en un formulario para romper o robar la base.

## El archivo `.env`

Guarda la configuración que cambia según la compu: usuario y contraseña de MySQL, nombre de la base, cómo se llama Python. **No se sube a GitHub** (está en `.gitignore`) porque tiene contraseñas. En el repo está `.env.example` como modelo.
