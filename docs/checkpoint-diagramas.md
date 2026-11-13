# Checkpoint "Lógica de Programación Aplicada" — Diagramas de flujo

Un diagrama de flujo por cada historia de usuario del MVP (ver [TP15](TP15-backlog-mvp.md)). La aplicación no tiene login (se quitó por indicación docente), así que ningún flujo pide sesión.
Convenciones: **óvalo** = inicio/fin, **rombo** = decisión, **paralelogramo** = entrada/salida de datos, **rectángulo** = proceso.
Cada diagrama también está exportado a PNG en [`docs/diagramas/`](diagramas/).

## US-1 Ver la landing

![US-1](diagramas/us1-landing.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/"Pedir la ruta /"/]
    B --> C[Armar lista de puertos de ejemplo]
    C --> D[Recorrer la lista y armar una fila por puerto]
    D --> E{¿Quedan puertos?}
    E -- Sí --> D
    E -- No --> G[/Mostrar pitch y tabla de ejemplo/]
    G --> Z([Fin])
```

## US-2 Nuevo escaneo de una IP privada

![US-2](diagramas/us2-nuevo-escaneo.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/Ingresar IP y notas/]
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
    N --> Z([Fin])
```

## US-3 Ver resultado con riesgo

![US-3](diagramas/us3-ver-resultado.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/"Pedir /escaneos/id"/]
    B --> C[Buscar escaneo por id]
    C --> D{¿Existe?}
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

## US-4 Historial de escaneos

![US-4](diagramas/us4-historial.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/"Pedir /escaneos con ?limit opcional"/]
    B --> C{¿Viene limit?}
    C -- No --> E[Listar todos los escaneos]
    C -- Sí --> D{¿limit es entero positivo?}
    D -- No --> F[/Mostrar error 400/] --> Z([Fin])
    D -- Sí --> G[Listar como máximo limit escaneos]
    E --> H{¿Hay escaneos?}
    G --> H
    H -- No --> I[/Mostrar 'Todavía no hay escaneos'/] --> Z
    H -- Sí --> J[/Mostrar tabla del más nuevo al más viejo/] --> Z
```

## US-5 Editar notas de un escaneo

![US-5](diagramas/us5-editar-notas.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/"Enviar PUT /escaneos/id con notas"/]
    B --> C{¿Existe el escaneo?}
    C -- No --> D[/Mostrar not_found 404/] --> Z([Fin])
    C -- Sí --> E{¿Notas de 255 caracteres o menos?}
    E -- No --> F[/Mostrar error 422/] --> Z
    E -- Sí --> G[Actualizar notas dentro de una transacción]
    G --> H[/Redirigir al detalle/] --> Z
```

## US-6 Borrar un escaneo

![US-6](diagramas/us6-borrar.png)

```mermaid
flowchart TD
    A([Inicio]) --> B[/Clic en 'Borrar'/]
    B --> C{¿El usuario confirma?}
    C -- No --> Z([Fin])
    C -- Sí --> D[/"Enviar DELETE /escaneos/id"/]
    D --> E{¿Existe el escaneo?}
    E -- No --> F[/Mostrar not_found 404/] --> Z
    E -- Sí --> G[Borrar escaneo en una transacción]
    G --> H[La base borra sus puertos por ON DELETE CASCADE]
    H --> I[/"Redirigir a /escaneos"/] --> Z
```
