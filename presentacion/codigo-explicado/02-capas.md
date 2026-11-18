# 2. Las capas (arquitectura del TP14)

El código está dividido en **capas**. Cada una tiene **un solo trabajo** y solo habla con la de al lado. Así, si mañana cambiamos la base de datos, solo tocamos la persistencia.

```
rutas  →  controladores  →  servicios  →  persistencia  →  base de datos
```

Una comparación para explicarlo: un **restaurante**.

| Capa | En el restaurante | En TraffiCop |
|---|---|---|
| Ruta | La carta: cada plato tiene su número | `GET /escaneos` → `EscaneoController::index` |
| Controlador | El mozo: toma el pedido, revisa que esté bien y te trae el plato | Valida los datos y elige la pantalla |
| Servicio | El cocinero: hace la comida | La lógica: escanear, guardar en una transacción |
| Persistencia | La despensa: solo el cocinero la usa | Las consultas SQL a la base |

## Rutas — `src/routes/`

**Qué son:** la lista de direcciones web que existen y qué función atiende cada una. **No tienen lógica**: solo conectan.

- `home.routes.php`: la página de inicio `/`.
- `escaneos.routes.php`: las 7 rutas del CRUD de escaneos, agrupadas bajo `/escaneos`.
- Se cargan con `require` desde `bootstrap.php`. Es un archivo por módulo (TP13).

**Respuesta tipo:** "las rutas definen qué función del controlador atiende cada dirección de la web".

## Controladores — `src/controllers/`

**Qué son:** reciben el pedido del usuario, **validan** los datos, llaman al servicio y deciden **qué pantalla mostrar y con qué código** (200, 201, 404, 422…).

- `HomeController::index` → arma los datos de ejemplo y muestra la landing.
- `EscaneoController` → todo el CRUD. Por ejemplo, `store` revisa que la IP sea privada y que las notas tengan 255 caracteres o menos.

**Respuesta tipo:** "el controlador recibe lo que mandó el usuario, lo valida y elige qué vista mostrar".

## Servicios — `src/services/`

**Qué son:** la **lógica de negocio**, o sea, las reglas del programa. No saben nada de HTML ni de direcciones web.

- `EscaneoService`: crear, listar, ver, actualizar notas y borrar. Tiene `enTransaccion()`.
- `ScannerService`: ejecuta el programa de Python y valida que la IP sea privada.

**Respuesta tipo:** "el servicio tiene la lógica: por ejemplo, ejecutar el escaneo y guardar el resultado todo junto".

## Persistencia — `src/persistence/`

**Qué es:** la **única** parte que habla con la base de datos. Acá están todas las consultas SQL.

- `Database::conexion()` → abre la conexión a MySQL con los datos del `.env`.
- `EscaneoRepository` → `listar`, `buscar`, `puertosDe`, `crear`, `agregarPuerto`, `actualizarNotas`, `borrar`.

**Respuesta tipo:** "la persistencia es la capa que guarda y lee los datos de la base".

## ¿Y `bootstrap.php`?

Es el archivo que **arma** la aplicación al arrancar: lee el `.env`, crea la app de Slim, agrega los middlewares, crea los objetos (repositorio → servicio → controlador) y carga las rutas.
