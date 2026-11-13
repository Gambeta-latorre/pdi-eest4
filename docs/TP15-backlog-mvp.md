# TP15 — Backlog del MVP de TraffiCop

Cada historia es un ticket (issue de GitHub creado con el template `historia-de-usuario`) en el [GitHub Project Kanban](https://github.com/users/Gambeta-latorre/projects/3). Las historias del MVP ya están hechas (columna **Done**); las de la versión 2 esperan en **Backlog**. Los criterios de aceptación se pueden **verificar** probando la aplicación (en el navegador o con `curl`).

> **Cambio de alcance:** por indicación docente, el registro y el inicio de sesión **no son necesarios** para el MVP y se quitaron de la aplicación. Cualquier persona que abra la web puede escanear y ver el historial.

| ID | Historia | Estado | Estimación |
|---|---|---|---|
| US-1 | Ver la landing | Done | 1 |
| US-2 | Nuevo escaneo de una IP privada | Done | 5 |
| US-3 | Ver resultado con riesgo | Done | 3 |
| US-4 | Historial de escaneos | Done | 2 |
| US-5 | Editar notas de un escaneo | Done | 2 |
| US-6 | Borrar un escaneo | Done | 1 |
| US-7 | Escanear un rango de IPs | Backlog | 5 |
| US-8 | Exportar el resultado a PDF | Backlog | 3 |
| US-9 | Comparar dos escaneos del mismo equipo | Backlog | 3 |

<!-- Cada sección "## US-x" de abajo es el cuerpo de un issue. -->

## US-1 Ver la landing

**Como** visitante
**quiero** ver una página de inicio que explique TraffiCop con un ejemplo
**para** entender qué hace antes de usarlo.

### Criterios de aceptación

- [ ] `GET /` responde 200.
- [ ] Muestra el elevator pitch y una tabla de ejemplo con puerto, servicio, riesgo y explicación.
- [ ] Tiene enlaces a "Escanear un equipo", "Historial" y "Nuevo escaneo".

## US-2 Nuevo escaneo de una IP privada

**Como** dueño de un pequeño negocio
**quiero** escribir la IP de un equipo de mi red y escanearlo
**para** saber qué puertos tiene abiertos.

### Criterios de aceptación

- [ ] En `GET /escaneos/create` hay un formulario con IP y notas opcionales.
- [ ] Con una IP privada (10.x, 172.16–31.x, 192.168.x) o 127.x, `POST /escaneos` ejecuta el escáner y responde 201 mostrando los datos enviados y el resultado.
- [ ] Con una IP pública (ej. 8.8.8.8) o un texto que no es una IP (ej. `127.0.0.1; dir`), se rechaza con 422 y no se ejecuta nada.
- [ ] El escaneo y sus puertos se guardan en una sola transacción (si falla algo, no queda nada a medias).

## US-3 Ver resultado con riesgo

**Como** usuario sin conocimientos técnicos
**quiero** ver cada puerto abierto con su riesgo y una explicación simple
**para** saber qué tengo que revisar o cerrar.

### Criterios de aceptación

- [ ] `GET /escaneos/{id}` muestra IP, fecha, notas y una tabla con puerto, servicio, riesgo y consejo.
- [ ] El riesgo se muestra como alto (rojo), medio (amarillo) o bajo (verde). Ejemplos: 23 telnet y 3389 rdp = alto; 22 ssh = medio; 443 https = bajo.
- [ ] Si no hay puertos abiertos se muestra un mensaje indicándolo.
- [ ] Si el escaneo no existe, se muestra la vista "Escaneo no encontrado" (404).

## US-4 Historial de escaneos

**Como** usuario
**quiero** ver la lista de los escaneos anteriores
**para** comparar cómo cambió mi red con el tiempo.

### Criterios de aceptación

- [ ] `GET /escaneos` lista los escaneos del más nuevo al más viejo, con IP, fecha, cantidad de puertos abiertos y notas.
- [ ] `GET /escaneos?limit=2` muestra como máximo 2 escaneos.
- [ ] Si `limit` no es un entero positivo (`abc`, `0`, `-1`, `2.5`) se muestra un error (400).
- [ ] Si no hay escaneos, se muestra "Todavía no hay escaneos para mostrar".

## US-5 Editar notas de un escaneo

**Como** usuario
**quiero** editar las notas de un escaneo
**para** recordar qué equipo era o qué cambios hice.

### Criterios de aceptación

- [ ] `GET /escaneos/{id}/edit` muestra un formulario con las notas actuales.
- [ ] El formulario envía `PUT /escaneos/{id}` (campo oculto `_METHOD=PUT`), guarda las notas en una transacción y redirige al detalle (302).
- [ ] Si las notas tienen más de 255 caracteres, se muestra el error y se responde 422 sin guardar.

## US-6 Borrar un escaneo

**Como** usuario
**quiero** borrar un escaneo que ya no me sirve
**para** mantener el historial ordenado.

### Criterios de aceptación

- [ ] En el detalle hay un botón "Borrar" que pide confirmación y envía `DELETE /escaneos/{id}` (campo oculto `_METHOD=DELETE`).
- [ ] Al borrar se redirige a `/escaneos` (302) y el escaneo ya no aparece; `GET /escaneos/{id}` responde 404.
- [ ] Sus puertos se borran solos de la tabla `puertos` (ON DELETE CASCADE).

## US-7 Escanear un rango de IPs

**Como** dueño de un pequeño negocio
**quiero** escanear toda mi red de una vez (por ejemplo 192.168.0.1 a 192.168.0.254)
**para** no tener que cargar los equipos uno por uno.

### Criterios de aceptación

- [ ] El formulario acepta un rango en formato CIDR (ej. `192.168.0.0/24`).
- [ ] Solo se aceptan rangos privados y de como máximo 256 direcciones; si no, error 422.
- [ ] El resultado muestra cada equipo que respondió con sus puertos abiertos.

## US-8 Exportar el resultado a PDF

**Como** usuario
**quiero** descargar el resultado de un escaneo en PDF
**para** guardarlo o mandárselo al técnico que me arregla la red.

### Criterios de aceptación

- [ ] En el detalle hay un botón "Descargar PDF".
- [ ] El PDF incluye IP, fecha, notas y la tabla de puertos con riesgo y consejo.
- [ ] Si el escaneo no existe, responde 404.

## US-9 Comparar dos escaneos del mismo equipo

**Como** usuario
**quiero** comparar dos escaneos de la misma IP
**para** ver qué puertos se abrieron o cerraron desde la última vez.

### Criterios de aceptación

- [ ] Desde el historial puedo elegir dos escaneos de la misma IP y compararlos.
- [ ] Se marcan en verde los puertos que se cerraron y en rojo los que se abrieron.
- [ ] Si las IPs son distintas, se muestra un error.
