<?php
/*
 * ============================================================================
 *  src/views/index.php  ->  LANDING (página de inicio)
 * ============================================================================
 *  Variables que manda HomeController: $titulo, $ejemplo (array de puertos mock).
 * ============================================================================
 */
?>
<section class="tarjeta">
    <h1>¿Qué puertos tiene abiertos tu red?</h1>
    <!-- La propuesta de valor: para pymes y hogares, en lenguaje simple. -->
    <p>TraffiCop ayuda a pequeños negocios y hogares a ver qué puertos tiene abiertos su red
       y qué riesgo implica cada uno, con una explicación simple.</p>
    <!-- Botón principal: lleva al formulario (GET /escaneos/create). -->
    <a class="boton" href="/escaneos/create">Escanear un equipo</a>
</section>

<section class="tarjeta">
    <h2>Ejemplo de resultado</h2>
    <p class="suave">Datos de ejemplo (no es un escaneo real).</p>
    <table>
        <tr><th>Puerto</th><th>Servicio</th><th>Riesgo</th><th>¿Qué significa?</th></tr>
        <!-- foreach con sintaxis alternativa (": ... endforeach;"): se lee
             mejor cuando se mezcla PHP con HTML. Una fila por puerto. -->
        <?php foreach ($ejemplo as $p): ?>
            <tr>
                <td><?= e($p['puerto']) ?></td>
                <td><?= e($p['servicio']) ?></td>
                <!-- La clase CSS depende del riesgo: riesgo-alto (rojo),
                     riesgo-medio (amarillo), riesgo-bajo (verde) = semáforo. -->
                <td><span class="riesgo riesgo-<?= e($p['riesgo']) ?>"><?= e($p['riesgo']) ?></span></td>
                <td><?= e($p['explicacion']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
