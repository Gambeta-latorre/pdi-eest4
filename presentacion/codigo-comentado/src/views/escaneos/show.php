<?php
/*
 * ============================================================================
 *  src/views/escaneos/show.php  ->  DETALLE / RESULTADO DE UN ESCANEO
 * ============================================================================
 *  Variables:
 *    $escaneo -> datos del escaneo + $escaneo['puertos'] (lista de abiertos).
 *    $enviado -> SOLO existe justo después de crear (store, status 201):
 *                muestra lo que el usuario mandó en el formulario.
 * ============================================================================
 */
?>
<!-- Bloque "Datos enviados": solo aparece recién creado el escaneo. -->
<?php if (!empty($enviado)): ?>
    <section class="tarjeta">
        <h2>Datos enviados</h2>
        <p>IP: <strong><?= e($enviado['objetivo']) ?></strong></p>
        <!-- Ternario: si escribió notas las mostramos; si no, "(sin notas)". -->
        <p>Notas: <?= e($enviado['notas'] !== '' ? $enviado['notas'] : '(sin notas)') ?></p>
    </section>
<?php endif; ?>

<section class="tarjeta">
    <h1>Escaneo #<?= e($escaneo['id']) ?> — <?= e($escaneo['objetivo']) ?></h1>
    <p class="suave">Fecha: <?= e($escaneo['fecha']) ?></p>
    <p>Notas: <?= e($escaneo['notas'] ?? '(sin notas)') ?></p>

    <?php if (empty($escaneo['puertos'])): ?>
        <!-- Ningún puerto abierto de los 18 revisados: buena noticia. -->
        <p>✅ No se encontraron puertos abiertos de la lista revisada.</p>
    <?php else: ?>
        <table>
            <tr><th>Puerto</th><th>Servicio</th><th>Riesgo</th><th>¿Qué hago?</th></tr>
            <?php foreach ($escaneo['puertos'] as $p): ?>
                <tr>
                    <td><?= e($p['puerto']) ?></td>
                    <td><?= e($p['servicio']) ?></td>
                    <!-- Semáforo de colores según el riesgo. -->
                    <td><span class="riesgo riesgo-<?= e($p['riesgo']) ?>"><?= e($p['riesgo']) ?></span></td>
                    <!-- Consejo en lenguaje simple (función de helpers.php). -->
                    <td><?= e(explicarRiesgo($p['riesgo'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="acciones">
        <!-- Link al formulario de edición: GET /escaneos/{id}/edit. -->
        <a class="boton" href="/escaneos/<?= e($escaneo['id']) ?>/edit">Editar notas</a>
        <!-- BORRAR: un link no puede hacer DELETE, así que usamos un formulario
             POST con el campo oculto _METHOD=DELETE. MethodOverrideMiddleware
             lo convierte en DELETE /escaneos/{id} -> destroy().
             confirm(...) pide confirmación en JavaScript antes de enviar. -->
        <form method="post" action="/escaneos/<?= e($escaneo['id']) ?>" onsubmit="return confirm('¿Borrar este escaneo?')">
            <input type="hidden" name="_METHOD" value="DELETE">
            <button type="submit" class="peligro">Borrar</button>
        </form>
        <a href="/escaneos" style="margin-top:14px">Volver al listado</a>
    </div>
</section>
