# Checkpoint "Lógica de Programación Aplicada" — Diagramas de flujo

Un diagrama de flujo por cada historia de usuario del MVP (ver [TP15](TP15-backlog-mvp.md)).
Convenciones: **óvalo** = inicio/fin, **rombo** = decisión, **paralelogramo** = entrada/salida de datos, **rectángulo** = proceso.
Cada diagrama también está exportado a PNG en [`docs/diagramas/`](diagramas/).

## HU-01 Registro de usuario

![HU-01](diagramas/hu01-registro.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/Ingresar nombre, email y contraseña/]
    B --> C{¿Están todos los campos?}
    C -- No --> E[/Mostrar error 422/] --> B
    C -- Sí --> D{¿Email válido y contraseña de 8+ caracteres?}
    D -- No --> E
    D -- Sí --> F{¿El email ya existe?}
    F -- Sí --> G[/Mostrar 'Ya existe una cuenta'/] --> B
    F -- No --> H[Guardar usuario con password_hash]
    H --> I[Iniciar sesión]
    I --> J[/Redirigir a /escaneos/]
    J --> K([Fin])
```

## HU-02 Inicio de sesión con email y contraseña

![HU-02](diagramas/hu02-login.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/Ingresar email y contraseña/]
    B --> C[Buscar usuario por email]
    C --> D{¿Existe el usuario?}
    D -- No --> F[/Mostrar 'Email o contraseña incorrectos' 401/] --> B
    D -- Sí --> E{¿password_verify es correcto?}
    E -- No --> F
    E -- Sí --> G[Regenerar id de sesión y guardar usuario]
    G --> H[/Redirigir a /escaneos/]
    H --> I([Fin])
```

## HU-03 Nuevo escaneo

![HU-03](diagramas/hu03-nuevo-escaneo.png)

```mermaid
flowchart TD
    A([Inicio]) --> S{¿Hay sesión iniciada?}
    S -- No --> L[/Redirigir a /auth/login/] --> Z([Fin])
    S -- Sí --> B[/Ingresar IP y notas/]
    B --> C{¿Es IPv4 privada o 127.x?}
    C -- No --> E[/Mostrar error 422/] --> B
    C -- Sí --> D{¿Notas de 255 caracteres o menos?}
    D -- No --> E
    D -- Sí --> F[Ejecutar escanear.py con proc_open]
    F --> G{¿El escáner respondió ok?}
    G -- No --> E
    G -- Sí --> H[Abrir transacción]
    H --> I[Guardar escaneo y cada puerto abierto]
    I --> J{¿Hubo error al guardar?}
    J -- Sí --> K[rollBack] --> E
    J -- No --> M[commit]
    M --> N[/Mostrar datos enviados y resultado 201/]
    N --> Z
```

## HU-04 Ver resultado con riesgo

![HU-04](diagramas/hu04-ver-resultado.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/Pedir /escaneos/id/]
    B --> C[Buscar escaneo por id y usuario]
    C --> D{¿Existe y es del usuario?}
    D -- No --> E[/Mostrar 'Escaneo no encontrado' 404/] --> Z([Fin])
    D -- Sí --> F{¿Tiene puertos abiertos?}
    F -- No --> G[/Mostrar 'No se encontraron puertos abiertos'/] --> Z
    F -- Sí --> H[Tomar el siguiente puerto]
    H --> I{¿Qué riesgo tiene?}
    I -- alto --> J[Etiqueta roja + 'Cerralo si no lo usás']
    I -- medio --> K[Etiqueta amarilla + 'Revisá la contraseña']
    I -- bajo --> L[Etiqueta verde + 'Mantenelo actualizado']
    J --> M{¿Quedan puertos?}
    K --> M
    L --> M
    M -- Sí --> H
    M -- No --> N[/Mostrar tabla de puertos/] --> Z
```

## HU-05 Historial de escaneos

![HU-05](diagramas/hu05-historial.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/Pedir /escaneos con ?limit opcional/]
    B --> C{¿Viene limit?}
    C -- No --> E[Listar todos los escaneos del usuario]
    C -- Sí --> D{¿limit es entero positivo?}
    D -- No --> F[/Mostrar error 400/] --> Z([Fin])
    D -- Sí --> G[Listar como máximo limit escaneos]
    E --> H{¿Hay escaneos?}
    G --> H
    H -- No --> I[/Mostrar 'Todavía no hay escaneos'/] --> Z
    H -- Sí --> J[/Mostrar tabla del más nuevo al más viejo/] --> Z
```

## HU-06 Editar notas de un escaneo

![HU-06](diagramas/hu06-editar-notas.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/Enviar PUT /escaneos/id con notas/]
    B --> C{¿Existe y es del usuario?}
    C -- No --> D[/Mostrar not_found 404/] --> Z([Fin])
    C -- Sí --> E{¿Notas de 255 caracteres o menos?}
    E -- No --> F[/Mostrar error 422/] --> Z
    E -- Sí --> G[Actualizar notas dentro de una transacción]
    G --> H[/Redirigir al detalle/] --> Z
```

## HU-07 Borrar un escaneo

![HU-07](diagramas/hu07-borrar.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/Clic en 'Borrar'/]
    B --> C{¿El usuario confirma?}
    C -- No --> Z([Fin])
    C -- Sí --> D[/Enviar DELETE /escaneos/id/]
    D --> E{¿Existe y es del usuario?}
    E -- No --> F[/Mostrar not_found 404/] --> Z
    E -- Sí --> G[Borrar escaneo en una transacción]
    G --> H[La base borra sus puertos por ON DELETE CASCADE]
    H --> I[/Redirigir a /escaneos/] --> Z
```

## HU-08 Ver la landing

![HU-08](diagramas/hu08-landing.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/Pedir la ruta //]
    B --> C[Armar lista de puertos de ejemplo]
    C --> D{¿Hay sesión iniciada?}
    D -- Sí --> E[Menú con 'Mis escaneos' y 'Salir']
    D -- No --> F[Menú con 'Iniciar sesión' y 'Crear cuenta']
    E --> G[/Mostrar pitch y tabla de ejemplo/]
    F --> G
    G --> Z([Fin])
```
