# Código de TraffiCop explicado

Material para estudiar las preguntas individuales. Los profes van a señalar algo del código y preguntar **"¿qué hace esto?"** o **"¿qué es esto?"**. Quieren respuestas cortas, con sus palabras y sin detalles línea por línea.

> Ejemplo que dieron: señalan `confirmar_compra` en la capa de servicio → "es la función que ejecuta la lógica de negocio cuando se confirma una compra".
> En nuestro proyecto sería: señalan `crear` en `EscaneoService` → "es la función que ejecuta el escaneo y guarda el resultado en la base".

## Orden para estudiar

1. [`01-panorama.md`](01-panorama.md): qué pasa desde que alguien aprieta "Escanear" hasta que ve el resultado. **Si entienden esto, entienden todo el proyecto.**
2. [`02-capas.md`](02-capas.md): para qué sirve cada carpeta (rutas, controladores, servicios, persistencia).
3. [`03-escaner-python.md`](03-escaner-python.md): cómo funciona el escaneo.
4. [`04-base-de-datos.md`](04-base-de-datos.md): tablas, migraciones, transacciones.
5. [`05-middlewares-y-vistas.md`](05-middlewares-y-vistas.md): middlewares, vistas y layout.
6. [`06-archivo-por-archivo.md`](06-archivo-por-archivo.md): una línea por cada archivo y cada función. Para repasar rápido.
7. [`07-preguntas-tipo.md`](07-preguntas-tipo.md): preguntas probables con la respuesta ideal. **Practíquenlas en voz alta, uno le pregunta al otro.**

## Mapa del proyecto

```
public/index.php            la puerta de entrada: toda visita pasa por acá
src/bootstrap.php           arma la aplicación (configuración, middlewares, rutas)
src/routes/                 qué dirección web va a qué función
src/controllers/            reciben el pedido, validan los datos y eligen qué pantalla mostrar
src/services/               la lógica del negocio (escanear, guardar en una transacción)
src/persistence/            lo único que habla con la base de datos
src/middlewares/            código que corre en todas las visitas (el registro de log)
src/views/                  las pantallas (HTML)
src/database/migrations/    los cambios de la base, numerados en orden
scripts/migrate.php         el programa que aplica esos cambios
scanner/escanear.py         el escáner de puertos, en Python
```
