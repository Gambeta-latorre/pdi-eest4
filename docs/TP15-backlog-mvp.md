# TP15 — Backlog del MVP de TraffiCop

Cada historia es un ticket (issue de GitHub creado con el template `historia-de-usuario`) y está en la columna **Todo** del [GitHub Project Kanban](https://github.com/users/Gambeta-latorre/projects/3). Los criterios de aceptación están escritos para que se puedan **verificar** probando la aplicación (en el navegador o con `curl`).

| ID | Historia | Prioridad | Estimación | Issue |
|---|---|---|---|---|
| HU-01 | Registro de usuario | Alta | 3 | [#2](https://github.com/Gambeta-latorre/pdi-eest4/issues/2) |
| HU-02 | Inicio de sesión con email y contraseña | Alta | 3 | [#1](https://github.com/Gambeta-latorre/pdi-eest4/issues/1) |
| HU-03 | Nuevo escaneo | Alta | 5 | [#3](https://github.com/Gambeta-latorre/pdi-eest4/issues/3) |
| HU-04 | Ver resultado con riesgo | Alta | 3 | [#4](https://github.com/Gambeta-latorre/pdi-eest4/issues/4) |
| HU-05 | Historial de escaneos | Media | 2 | [#5](https://github.com/Gambeta-latorre/pdi-eest4/issues/5) |
| HU-06 | Editar notas de un escaneo | Media | 2 | [#6](https://github.com/Gambeta-latorre/pdi-eest4/issues/6) |
| HU-07 | Borrar un escaneo | Media | 1 | [#7](https://github.com/Gambeta-latorre/pdi-eest4/issues/7) |
| HU-08 | Ver la landing | Baja | 1 | [#8](https://github.com/Gambeta-latorre/pdi-eest4/issues/8) |

<!-- Cada sección "## HU-xx" de abajo es el cuerpo de un issue. -->

## HU-01 Registro de usuario

**Como** persona que quiere revisar su red
**quiero** crear una cuenta con mi nombre, email y contraseña
**para** guardar mis escaneos y que solo yo pueda verlos.

### Criterios de aceptación

- [ ] En `GET /auth/register` se ve un formulario con nombre, email y contraseña.
- [ ] Con datos válidos, `POST /auth/register` crea el usuario, inicia la sesión y redirige a `/escaneos` (302).
- [ ] Si falta algún campo, el email no es válido o la contraseña tiene menos de 8 caracteres, se muestra el error en el formulario (422) y no se crea el usuario.
- [ ] Si el email ya está registrado, se muestra "Ya existe una cuenta con ese email" (422).
- [ ] En la tabla `usuarios` la contraseña queda guardada como hash, nunca en texto plano.

## HU-02 Inicio de sesión con email y contraseña

**Como** usuario registrado
**quiero** iniciar sesión con mi email y mi contraseña
**para** acceder a mis escaneos guardados.

### Criterios de aceptación

- [ ] Con email y contraseña correctos, `POST /auth/login` inicia la sesión y redirige a `/escaneos` (302).
- [ ] Con datos incorrectos se muestra "Email o contraseña incorrectos" (401), sin decir cuál de los dos falló.
- [ ] Sin sesión, cualquier ruta de `/escaneos` redirige a `/auth/login` (302).
- [ ] `/auth/logout` cierra la sesión y redirige a `/`.

## HU-03 Nuevo escaneo

**Como** usuario logueado
**quiero** escribir la IP de un equipo de mi red y escanearlo
**para** saber qué puertos tiene abiertos.

### Criterios de aceptación

- [ ] En `GET /escaneos/create` hay un formulario con IP y notas opcionales.
- [ ] Con una IP privada (10.x, 172.16–31.x, 192.168.x) o 127.x, `POST /escaneos` ejecuta el escáner y responde 201 mostrando los datos enviados y el resultado.
- [ ] Con una IP pública (ej. 8.8.8.8) o un texto que no es una IP (ej. `127.0.0.1; dir`), se rechaza con 422 y no se ejecuta nada.
- [ ] El escaneo y sus puertos se guardan en una sola transacción (si falla algo, no queda nada a medias).

## HU-04 Ver resultado con riesgo

**Como** usuario sin conocimientos técnicos
**quiero** ver cada puerto abierto con su riesgo y una explicación simple
**para** saber qué tengo que revisar o cerrar.

### Criterios de aceptación

- [ ] `GET /escaneos/{id}` muestra IP, fecha, notas y una tabla con puerto, servicio, riesgo y consejo.
- [ ] El riesgo se muestra como alto (rojo), medio (amarillo) o bajo (verde). Ejemplos: 23 telnet y 3389 rdp = alto; 22 ssh = medio; 443 https = bajo.
- [ ] Si no hay puertos abiertos se muestra un mensaje indicándolo.
- [ ] Si el escaneo no existe o es de otro usuario, se muestra la vista "Escaneo no encontrado" (404).

## HU-05 Historial de escaneos

**Como** usuario logueado
**quiero** ver la lista de mis escaneos anteriores
**para** comparar cómo cambió mi red con el tiempo.

### Criterios de aceptación

- [ ] `GET /escaneos` lista solo mis escaneos, del más nuevo al más viejo, con IP, fecha, cantidad de puertos abiertos y notas.
- [ ] `GET /escaneos?limit=2` muestra como máximo 2 escaneos.
- [ ] Si `limit` no es un entero positivo (`abc`, `0`, `-1`, `2.5`) se muestra un error (400).
- [ ] Si no tengo escaneos, se muestra "Todavía no hay escaneos para mostrar".

## HU-06 Editar notas de un escaneo

**Como** usuario logueado
**quiero** editar las notas de un escaneo
**para** recordar qué equipo era o qué cambios hice.

### Criterios de aceptación

- [ ] `GET /escaneos/{id}/edit` muestra un formulario con las notas actuales.
- [ ] El formulario envía `PUT /escaneos/{id}` (campo oculto `_METHOD=PUT`), guarda las notas en una transacción y redirige al detalle (302).
- [ ] Si las notas tienen más de 255 caracteres, se muestra el error y se responde 422 sin guardar.

## HU-07 Borrar un escaneo

**Como** usuario logueado
**quiero** borrar un escaneo que ya no me sirve
**para** mantener mi historial ordenado.

### Criterios de aceptación

- [ ] En el detalle hay un botón "Borrar" que pide confirmación y envía `DELETE /escaneos/{id}` (campo oculto `_METHOD=DELETE`).
- [ ] Al borrar se redirige a `/escaneos` (302) y el escaneo ya no aparece; `GET /escaneos/{id}` responde 404.
- [ ] Sus puertos se borran solos de la tabla `puertos` (ON DELETE CASCADE).
- [ ] No se puede borrar un escaneo de otro usuario (404).

## HU-08 Ver la landing

**Como** visitante
**quiero** ver una página de inicio que explique TraffiCop con un ejemplo
**para** decidir si me sirve antes de crear una cuenta.

### Criterios de aceptación

- [ ] `GET /` responde 200 sin necesidad de iniciar sesión.
- [ ] Muestra el elevator pitch y una tabla de ejemplo con puertos, servicio, riesgo y explicación.
- [ ] Tiene enlaces a "Iniciar sesión", "Crear cuenta" y "Escanear un equipo".
