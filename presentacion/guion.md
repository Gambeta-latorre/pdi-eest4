# Guión de la exposición — TraffiCop

**Fecha:** miércoles 18/11 (si no llegan, sigue el 25/11 — tienen que ir los dos ambos días)
**Duración:** 15 minutos · **Exponen:** Juan Cruz (JC) y Lorenzo Mondo (LM)
**Qué piden los profes:** 1) vender el producto a alguien que no sabe del tema, 2) retrospectiva, 3) demo del MVP. Después, una pregunta conceptual sobre el código a cada uno (ver la carpeta `codigo-explicado/`).

> Regla de oro: en la parte del producto **no se habla de código**. Nada de "Slim", "PDO" ni "controlador". Hablen como si le explicaran a un tío que tiene un kiosco.

## Reparto del tiempo

| Min | Parte | Diapositiva | Quién |
|---|---|---|---|
| 0:00 – 1:00 | Apertura | 1 | LM |
| 1:00 – 2:30 | El problema | 2 | JC |
| 2:30 – 4:30 | La solución + elevator pitch | 3, 4, 5 | LM |
| 4:30 – 5:30 | Para quién es | 6 | JC |
| 5:30 – 10:30 | Demo | 7 | JC maneja la compu, LM narra |
| 10:30 – 14:00 | Retrospectiva | 8 a 11 | LM y JC (mitad cada uno) |
| 14:00 – 15:00 | Lo que viene + cierre | 12, 13 | JC |

Si van atrasados, recorten la diapositiva 6 y acorten la demo (saltear "editar notas").

---

## 1. Apertura — LM (1 min) · Diapositiva 1

> "Buen día. Una pregunta: ¿alguien sabe qué 'puertas' tiene abiertas su compu o el router de su casa?
> (pausa)
> Casi nadie lo sabe. Y por esas puertas es por donde entran muchos ataques. Nosotros somos Juan Cruz y Lorenzo, y les vamos a presentar **TraffiCop**."

## 2. El problema — JC (1:30) · Diapositiva 2

> "Pensemos en un kiosco, una peluquería o una casa con cámaras y Wi-Fi. Tienen equipos conectados a la red, pero nadie revisa si quedaron 'puertas' abiertas.
> Una puerta abierta es un **puerto**: un canal por donde un programa se comunica. Algunos son normales, como el de las páginas web. Otros son peligrosos, como los que permiten manejar la compu a distancia o los que mandan contraseñas sin protección.
> El problema es que las herramientas que existen para revisar esto son para técnicos: muestran números y siglas que una persona común no entiende. Y contratar a alguien cuesta plata."

## 3. La solución — LM (2 min) · Diapositivas 3, 4 y 5

**Elevator pitch (decirlo tal cual, es el que ya tenemos escrito):**

> "**TraffiCop ayuda a pequeños negocios y hogares a ver qué puertos tiene abiertos su red y qué riesgo implica cada uno, con una explicación simple.**"

> "¿Cómo se usa? Tres pasos (diapositiva 4):
> 1. Escribís la dirección del equipo que querés revisar: tu compu, el router, una cámara.
> 2. TraffiCop lo revisa en unos segundos.
> 3. Te muestra qué puertas encontró abiertas, cada una con un **semáforo**: rojo, amarillo o verde, y una frase que te dice qué hacer."

> "(diapositiva 5) Por ejemplo: si aparece el puerto del escritorio remoto, sale en **rojo** con 'cerralo si no lo usás'. Si aparece el de las páginas web seguras, sale en **verde**: es normal.
> Y algo importante: **solo deja revisar equipos de tu propia red**. No se puede usar para espiar a otros."

## 4. Para quién es — JC (1 min) · Diapositiva 6

> "Está pensado para el dueño de un comercio chico, para una familia o para alguien que trabaja desde su casa: gente que no sabe de redes y no tiene un técnico.
> Lo que gana: saber en un minuto si está expuesto, entender por qué y guardar un historial para ver si después de arreglar algo, la puerta realmente se cerró."

---

## 5. Demo — JC maneja, LM narra (5 min) · Diapositiva 7

**Antes de la clase (checklist):**

- [ ] Encender MySQL de XAMPP (`C:\riquelme\xampp-control.exe` → Start en MySQL).
- [ ] En la carpeta del proyecto: `composer migrate` y después `php -S localhost:8000 -t public`.
- [ ] Abrir <http://localhost:8000> en el navegador y dejarlo en pantalla completa.
- [ ] Hacer un escaneo de prueba a `127.0.0.1` para que el historial no esté vacío.
- [ ] **Plan B:** el video de la demo (1:24, con subtítulos) está dentro de la diapositiva 7: clic para reproducir. También está suelto como `TraffiCop-demo.mp4`; llévenlo en un pendrive por las dudas.

**Paso a paso (lo que se hace → lo que se dice):**

| # | JC hace | LM dice |
|---|---|---|
| 1 | Muestra la página de inicio | "Esta es la página de inicio. Arriba está la frase del producto y abajo un ejemplo de resultado, para que entiendas qué vas a ver antes de usarlo." |
| 2 | Clic en **Nuevo escaneo** | "Vamos a revisar esta misma compu." |
| 3 | Escribe `8.8.8.8` y aprieta Escanear | "Primero probamos algo que **no** se debe poder: esta es una dirección de Internet, de Google. TraffiCop la rechaza, porque solo revisa tu propia red." |
| 4 | Escribe `127.0.0.1`, nota "Compu del aula", Escanear | "Ahora sí: 127.0.0.1 quiere decir 'esta misma compu'." |
| 5 | Muestra el resultado | "En segundos aparece qué puertas están abiertas. Cada una tiene su color y su consejo. Por ejemplo, esta en rojo dice 'cerralo si no lo usás'." |
| 6 | Clic en **Historial** | "Todo queda guardado en un historial, del más nuevo al más viejo." |
| 7 | Entra a un escaneo → **Editar notas** → cambia el texto → Guardar | "Le podés poner notas, por ejemplo 'router de la oficina', para acordarte qué equipo era." |
| 8 | **Borrar** → confirmar | "Y si un escaneo ya no te sirve, lo borrás." |

> Si algo falla en vivo, no se pongan nerviosos: "por las dudas trajimos el video", vuelven a la diapositiva 7, clic en el video y lo narran encima.

---

## 6. Retrospectiva — LM (diapositivas 8 a 10) y JC (diapositiva 11) · 3:30 min

> Esta parte es **la de ustedes**: los profes quieren escuchar cómo lo vivieron. Lo que está entre [corchetes] completenlo con lo que les pasó de verdad. No inventen: si algo no pasó, sáquenlo.

**Cómo trabajamos (LM, diapositivas 8 y 9):**

> "Trabajamos con **Scrum**: armamos un backlog con las historias de usuario, por ejemplo 'como dueño de un negocio quiero escanear un equipo de mi red para saber qué puertos tiene abiertos', y las fuimos moviendo en un tablero **Kanban** en GitHub: Backlog, Ready, In progress, In review y Done.
> Primero hicimos el mapa de historias para decidir qué entraba en el MVP y qué quedaba para después.
> [Contá cómo se repartieron el trabajo entre los dos y cada cuánto se juntaban.]"

**Qué salió bien (LM, diapositiva 10):**

> "Llegamos con el MVP completo: escanear, ver el riesgo, historial, editar notas y borrar. Las 6 historias del MVP están en Done.
> [Qué les gustó de Scrum: por ejemplo, ver el avance en el tablero, dividir algo grande en partes chicas.]"

**Problemas y cómo los resolvimos (JC, diapositiva 11):**

> "Tuvimos varios desafíos:
> - **Cambio de alcance:** al principio la app tenía registro e inicio de sesión. En una revisión, los profes nos dijeron que para el MVP no hacía falta, así que lo sacamos y nos enfocamos en lo importante: el escaneo. Eso es justamente lo que propone Scrum: adaptarse cuando cambia lo que se necesita.
> - **Que no se use para mal:** una herramienta que revisa puertos podría usarse para atacar a otros. Lo resolvimos haciendo que **solo** acepte direcciones de la red local.
> - **Hacer hablar dos lenguajes:** la web está hecha en un lenguaje y el escaneo en otro. Hubo que encontrar una forma segura de que uno le pase el trabajo al otro.
> - [Algún problema real que hayan tenido: algo que no andaba, una herramienta que no se instalaba, organizarse con los tiempos, etc., y cómo lo destrabaron.]"

**Qué nos llevamos:**

> "[Una frase cada uno: qué aprendieron o qué harían distinto la próxima.]"

---

## 7. Lo que viene + cierre — JC (1 min) · Diapositivas 12 y 13

> "Lo que sigue en el backlog: revisar **toda la red de una vez** en lugar de equipo por equipo, **descargar el resultado en PDF** para mandárselo a un técnico, y **comparar dos escaneos** para ver qué cambió."

> "Para cerrar: **TraffiCop ayuda a pequeños negocios y hogares a ver qué puertos tiene abiertos su red y qué riesgo implica cada uno, con una explicación simple.**
> Muchas gracias. ¿Preguntas?"

---

## Preguntas que les pueden hacer del producto (respuesta corta)

- **¿Es legal?** Sí, porque solo revisa equipos de tu propia red, que son tuyos. Por eso bloquea cualquier dirección de Internet.
- **¿Cierra los puertos solo?** No, te dice cuáles revisar y qué hacer. Cerrarlos depende del router o del programa. Eso podría ser una mejora futura.
- **¿Por qué no usar una herramienta que ya existe?** Las que existen son para técnicos. La diferencia de TraffiCop es la explicación simple y el semáforo.
- **¿Qué puertos revisa?** Los 18 más comunes (web, correo, escritorio remoto, carpetas compartidas, bases de datos, etc.).
- **¿Por qué sacaron el login?** Por indicación de los docentes no era necesario para el MVP. Quedó como posible mejora.

Para las preguntas sobre el **código**, estudien `codigo-explicado/` (sobre todo `07-preguntas-tipo.md`).
