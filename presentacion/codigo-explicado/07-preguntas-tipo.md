# 7. Preguntas tipo con respuesta ideal

Respuestas cortas, con palabras propias. Practiquen: uno lee la pregunta y el otro contesta **sin mirar**.

## Conceptos generales

**¿Qué es una API?**
Una forma en que dos programas se comunican: uno pide algo y el otro responde. En la web, son direcciones que reciben pedidos y devuelven datos o páginas. Por ejemplo, `GET /escaneos` es un pedido para ver el historial.

**¿Qué es HTTP? ¿Qué son GET, POST, PUT y DELETE?**
HTTP es el idioma con el que el navegador y el servidor se hablan. Los métodos dicen qué querés hacer: GET es pedir/ver, POST es crear, PUT es modificar y DELETE es borrar.

**¿Qué es un framework? ¿Qué es Slim?**
Una base de código ya hecha que resuelve lo repetitivo. Slim es un framework chico de PHP que maneja las rutas, los middlewares y las respuestas, así nosotros escribimos solo la lógica.

**¿Qué es un MVP?**
El Producto Mínimo Viable: la versión más chica que ya sirve y se puede mostrar. El nuestro escanea, muestra el riesgo y guarda un historial.

**¿Qué es una arquitectura en capas?**
Dividir el código en partes con un solo trabajo cada una (rutas, controladores, servicios, persistencia), para que sea más ordenado y fácil de cambiar.

**¿Qué es el backend y qué es el frontend?**
El frontend es lo que ve el usuario (las pantallas en el navegador). El backend es lo que corre en el servidor: valida, escanea y guarda en la base.

## Señalando código

**`EscaneoService::crear`**: "Es la función que ejecuta el escaneo y guarda el resultado en la base, todo dentro de una transacción."

**`EscaneoService::enTransaccion`**: "Es la función que hace que varias operaciones se guarden todas juntas o ninguna. Si algo falla, deshace todo."

**`EscaneoController::store`**: "Es la función que recibe el formulario de nuevo escaneo, valida los datos y muestra el resultado."

**`EscaneoController::index`**: "Es la función que muestra el historial, y valida el `limit` si viene."

**`ScannerService::escanear`**: "Es la función que ejecuta el programa de Python y trae su resultado."

**`ScannerService::ipPermitida`** / **`ip_permitida`** en Python: "Es la que revisa que la IP sea de la red local, para que no se pueda escanear a otros."

**`EscaneoRepository`**: "Es la clase que habla con la base de datos: tiene las consultas para guardar, buscar, editar y borrar escaneos."

**`Database::conexion`**: "Es la que abre la conexión a la base de datos."

**`LogMiddleware`**: "Es el middleware que anota cada visita a la web, con la ruta, el resultado y cuánto tardó."

**`escaneos.routes.php`**: "Es el archivo que dice qué función atiende cada dirección de escaneos."

**`layout.php`**: "Es el molde de todas las pantallas, con el menú y los estilos."

**`bootstrap.php`**: "Es el archivo que arma la aplicación al arrancar."

**`probar_puerto`** (Python): "Es la que intenta conectarse a un puerto para saber si está abierto."

**`ThreadPoolExecutor`** (Python): "Sirve para probar varios puertos al mismo tiempo usando hilos."

**Una migración, por ejemplo `004_crear_puertos.sql`**: "Es un cambio en la base de datos: esta crea la tabla de puertos."

**`scripts/migrate.php`**: "Es el programa que aplica en orden las migraciones que faltan."

**`$this->pdo->prepare(...)`**: "Prepara una consulta con huecos para los datos. Eso evita la inyección SQL."

**`proc_open([...])`**: "Ejecuta otro programa, en este caso el escáner de Python. Le pasamos los datos como lista para que nadie pueda colar comandos."

**`_METHOD` en un formulario**: "Es un campo oculto para poder mandar PUT o DELETE desde un formulario, que solo sabe mandar GET y POST."

**`e()`**: "Escapa el texto antes de mostrarlo, para que nadie pueda meter código en la página."

**`ON DELETE CASCADE`**: "Hace que al borrar un escaneo, la base borre sola sus puertos."

**`.env`**: "Es la configuración con los datos de la base. No se sube a GitHub porque tiene contraseñas."

**`composer.json`**: "Es la lista de librerías que usa el proyecto."

## Preguntas de "¿por qué?"

**¿Por qué el escaneo está en Python y no en PHP?**
Python tiene herramientas simples para conexiones de red e hilos, y así cada lenguaje hace lo que mejor sabe: PHP la web y Python el escaneo.

**¿Por qué solo se pueden escanear IPs privadas?**
Por seguridad y por ética: escanear equipos ajenos puede ser un ataque. La app es para revisar tu propia red.

**¿Por qué usan transacciones?**
Para que un escaneo nunca quede guardado a medias: o se guarda con todos sus puertos, o no se guarda nada.

**¿Por qué separan en capas?**
Para que cada parte tenga un solo trabajo. Es más fácil de entender, de probar y de cambiar.

**¿Por qué hay una tabla `puertos` aparte?**
Porque un escaneo puede tener muchos puertos abiertos (relación uno a muchos). Así se puede consultar y ordenar cada puerto.

**¿Qué pasa si pongo `/escaneos/999`?**
El controlador no lo encuentra y muestra la vista "Escaneo no encontrado", con código 404.

**¿Qué pasa si pongo `?limit=abc`?**
El controlador valida que sea un número entero positivo; como no lo es, muestra un error con código 400.
