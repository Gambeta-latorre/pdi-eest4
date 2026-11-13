<?php if (!empty($enviado)): ?>
    <section class="tarjeta">
        <h2>Datos enviados</h2>
        <p>IP: <strong><?= e($enviado['objetivo']) ?></strong></p>
        <p>Notas: <?= e($enviado['notas'] !== '' ? $enviado['notas'] : '(sin notas)') ?></p>
    </section>
<?php endif; ?>

<section class="tarjeta">
    <h1>Escaneo #<?= e($escaneo['id']) ?> — <?= e($escaneo['objetivo']) ?></h1>
    <p class="suave">Fecha: <?= e($escaneo['fecha']) ?></p>
    <p>Notas: <?= e($escaneo['notas'] ?? '(sin notas)') ?></p>

    <?php if (empty($escaneo['puertos'])): ?>
        <p>✅ No se encontraron puertos abiertos de la lista revisada.</p>
    <?php else: ?>
        <table>
            <tr><th>Puerto</th><th>Servicio</th><th>Riesgo</th><th>¿Qué hago?</th></tr>
            <?php foreach ($escaneo['puertos'] as $p): ?>
                <tr>
                    <td><?= e($p['puerto']) ?></td>
                    <td><?= e($p['servicio']) ?></td>
                    <td><span class="riesgo riesgo-<?= e($p['riesgo']) ?>"><?= e($p['riesgo']) ?></span></td>
                    <td><?= e(explicarRiesgo($p['riesgo'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="acciones">
        <a class="boton" href="/escaneos/<?= e($escaneo['id']) ?>/edit">Editar notas</a>
        <form method="post" action="/escaneos/<?= e($escaneo['id']) ?>" onsubmit="return confirm('¿Borrar este escaneo?')">
            <input type="hidden" name="_METHOD" value="DELETE">
            <button type="submit" class="peligro">Borrar</button>
        </form>
        <a href="/escaneos" style="margin-top:14px">Volver al listado</a>
    </div>
</section>
