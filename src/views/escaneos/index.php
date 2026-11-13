<section class="tarjeta">
    <div class="acciones" style="justify-content:space-between">
        <h1>Historial de escaneos</h1>
        <a class="boton" href="/escaneos/create">Nuevo escaneo</a>
    </div>

    <?php if (!empty($error)): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <?php if ($limite !== null): ?>
        <p class="suave">Mostrando como máximo <?= e($limite) ?>. <a href="/escaneos">Ver todos</a></p>
    <?php endif; ?>

    <?php if (empty($escaneos)): ?>
        <p>Todavía no hay escaneos para mostrar.</p>
    <?php else: ?>
        <table>
            <tr><th>#</th><th>Objetivo</th><th>Fecha</th><th>Abiertos</th><th>Notas</th></tr>
            <?php foreach ($escaneos as $escaneo): ?>
                <tr>
                    <td><a href="/escaneos/<?= e($escaneo['id']) ?>"><?= e($escaneo['id']) ?></a></td>
                    <td><?= e($escaneo['objetivo']) ?></td>
                    <td><?= e($escaneo['fecha']) ?></td>
                    <td><?= e($escaneo['abiertos']) ?></td>
                    <td><?= e($escaneo['notas'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>
