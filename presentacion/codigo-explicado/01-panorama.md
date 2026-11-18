# 1. Panorama: el viaje de un escaneo

TraffiCop es una **aplicación web**: el usuario usa el navegador, y del otro lado hay un **servidor** que hace el trabajo y guarda todo en una **base de datos**.

Tiene dos programas que trabajan juntos:

- **La web**, hecha en **PHP** con el framework **Slim**. Muestra las pantallas, valida los datos y guarda en la base.
- **El escáner**, hecho en **Python**. Es el que realmente prueba los puertos.

## Qué pasa cuando alguien aprieta "Escanear"

```
Navegador ──POST /escaneos──▶ public/index.php
                                  │
                         LogMiddleware (anota la visita)
                                  │
                         Ruta: escaneos.routes.php  ──▶ "esto lo atiende EscaneoController::store"
                                  │
                         Controlador: valida la IP y las notas
                                  │   (si están mal → vuelve el formulario con el error, código 422)
                                  ▼
                         Servicio: EscaneoService::crear
                                  │   1) le pide al ScannerService que ejecute el Python
                                  │   2) abre una transacción y guarda escaneo + puertos
                                  ▼
                         Persistencia: EscaneoRepository (las consultas SQL)
                                  │
                                  ▼
                         Base de datos MySQL
                                  │
          ◀──── la respuesta vuelve al controlador, que muestra la vista show.php (código 201)
```

**En una frase:** la ruta dice quién atiende, el controlador revisa los datos, el servicio hace el trabajo y la persistencia guarda en la base. Después el controlador muestra la pantalla con el resultado.

## Ejemplo con datos reales

1. El usuario escribe `127.0.0.1` (su propia compu) y la nota "Compu del aula".
2. El **controlador** se fija que sea una IP privada. `8.8.8.8` se rechaza.
3. El **servicio** ejecuta `python scanner/escanear.py 127.0.0.1`.
4. Python prueba 18 puertos **al mismo tiempo** (con hilos) y devuelve un JSON como este:
   ```json
   {"ok": true, "objetivo": "127.0.0.1", "abiertos": [
     {"puerto": 445, "servicio": "smb", "riesgo": "alto"},
     {"puerto": 3306, "servicio": "mysql", "riesgo": "medio"}]}
   ```
5. El **servicio** guarda una fila en `escaneos` y una fila por cada puerto en `puertos`, todo dentro de una **transacción**.
6. La **vista** muestra la tabla con el semáforo (rojo, amarillo o verde) y el consejo.

## Las otras acciones (CRUD)

CRUD = **C**reate (crear), **R**ead (leer), **U**pdate (actualizar), **D**elete (borrar).

| Acción | Dirección | Qué hace |
|---|---|---|
| Crear | `POST /escaneos` | escanea y guarda |
| Leer todos | `GET /escaneos` | historial (acepta `?limit=N`) |
| Leer uno | `GET /escaneos/5` | detalle del escaneo 5 |
| Actualizar | `PUT /escaneos/5` | cambia las notas |
| Borrar | `DELETE /escaneos/5` | borra el escaneo y sus puertos |
