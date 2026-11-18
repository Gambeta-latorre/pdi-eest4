<?php
/*
 * ============================================================================
 *  src/helpers.php  ->  FUNCIONES AYUDANTES PARA LAS VISTAS
 * ============================================================================
 *  Son funciones sueltas (no clases). Composer las carga siempre porque están
 *  en composer.json -> "autoload" -> "files". Por eso las vistas pueden usar
 *  e(...) y explicarRiesgo(...) sin hacer ningún require.
 * ============================================================================
 */

/*
 * e()  ->  ESCAPAR TEXTO (protección contra XSS)
 *
 * XSS (Cross-Site Scripting) = que alguien escriba código HTML/JavaScript en
 * un campo (por ejemplo en "notas": <script>alert('hack')</script>) y que ese
 * código se ejecute cuando otro abra la página.
 *
 * htmlspecialchars convierte los caracteres peligrosos en texto inofensivo:
 *    <  ->  &lt;      >  ->  &gt;      "  ->  &quot;      '  ->  &#039;
 * Entonces el navegador MUESTRA el texto en vez de ejecutarlo.
 *
 * - mixed $valor: acepta cualquier tipo (número, texto, null).
 * - (string): lo convertimos a texto antes de escapar.
 * - ENT_QUOTES: escapa comillas dobles Y simples (importante dentro de atributos).
 * - 'UTF-8': para que tildes y ñ se manejen bien.
 *
 * Regla del proyecto: TODO dato que se imprime en una vista pasa por e().
 */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/*
 * explicarRiesgo()  ->  CONSEJO SIMPLE SEGÚN EL NIVEL DE RIESGO
 *
 * Es la parte "para no expertos" de TraffiCop: además del semáforo
 * (alto/medio/bajo), le decimos al usuario QUÉ HACER en lenguaje simple.
 *
 * match es como un switch pero más corto y estricto (compara con ===) y
 * devuelve un valor. "default" cubre cualquier otro caso (o sea, 'bajo').
 */
function explicarRiesgo(string $riesgo): string
{
    return match ($riesgo) {
        'alto'  => 'Cerralo si no lo usás: es un servicio muy atacado o que viaja sin cifrar.',
        'medio' => 'Revisá que tenga contraseña fuerte y que no esté expuesto a Internet.',
        default => 'Normal en muchos equipos, pero mantené el software actualizado.',
    };
}
