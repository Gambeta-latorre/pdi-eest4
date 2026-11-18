<?php
/*
 * ============================================================================
 *  src/views/escaneos/index.php  ->  HISTORIAL (listado de escaneos)
 * ============================================================================
 *  Variables que manda EscaneoController::index():
 *    $escaneos -> lista de escaneos (con la cantidad de puertos "abiertos").
 *    $limite   -> el número de ?limit=N, o null si se muestran todos.
 *    $error    -> solo si ?limit era inválido (status 400).
 * ============================================================================
 */
?>
<section class="tarjeta">
    <div class="acciones" style="justify-content:space-between">
        <h1>Historial de escaneos</h1>
        <a class="boton" href="/escaneos/create">Nuevo escaneo</a>
    </div>

    <?php if (!empty($error)): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <!-- Si hay límite, avisamos y damos un link para ver todos. -->
    <?php if ($limite !== null): ?>
        <p class="suave">Mostrando como máximo <?= e($limite) ?>. <a href="/escaneos">Ver todos</a></p>
    <?php endif; ?>

    <!-- Si no hay escaneos mostramos un mensaje en vez de una tabla vacía. -->
    <?php if (empty($escaneos)): ?>
        <p>Todavía no hay escaneos para mostrar.</p>
    <?php else: ?>
        <table>
            <tr><th>#</th><th>Objetivo</th><th>Fecha</th><th>Abiertos</th><th>Notas</th></tr>
            <?php foreach ($escaneos as $escaneo): ?>
                <tr>
                    <!-- El número es un link al detalle: GET /escaneos/{id}. -->
                    <td><a href="/escaneos/<?= e($escaneo['id']) ?>"><?= e($escaneo['id']) ?></a></td>
                    <td><?= e($escaneo['objetivo']) ?></td>
                    <td><?= e($escaneo['fecha']) ?></td>
                    <!-- "abiertos" viene del COUNT(p.id) del repositorio. -->
                    <td><?= e($escaneo['abiertos']) ?></td>
                    <!-- Las notas pueden ser NULL -> mostramos vacío. -->
                    <td><?= e($escaneo['notas'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>
