<?php
/*
 * ============================================================================
 *  src/views/escaneos/update.php  ->  FORMULARIO "EDITAR NOTAS"
 * ============================================================================
 *  Lo muestran edit() (primera vez) y update() si las notas superan los
 *  255 caracteres (status 422, con $error).
 *  Solo se editan las notas: el resultado del escaneo no se modifica.
 * ============================================================================
 */
?>
<section class="tarjeta">
    <h1>Editar notas del escaneo #<?= e($escaneo['id']) ?></h1>
    <p class="suave">Objetivo: <?= e($escaneo['objetivo']) ?> · <?= e($escaneo['fecha']) ?></p>
    <?php if (!empty($error)): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <!-- Formulario POST con _METHOD=PUT -> se convierte en PUT /escaneos/{id}
         -> EscaneoController::update(). -->
    <form method="post" action="/escaneos/<?= e($escaneo['id']) ?>">
        <input type="hidden" name="_METHOD" value="PUT">
        <label for="notas">Notas (máximo 255 caracteres)</label>
        <!-- Precargado con las notas actuales (o vacío si son NULL).
             No tiene maxlength, pero no importa: el servidor controla el
             largo y, si se pasa de 255, devuelve 422 con el error. -->
        <textarea id="notas" name="notas" rows="4"><?= e($escaneo['notas'] ?? '') ?></textarea>
        <button type="submit">Guardar</button>
        <a href="/escaneos/<?= e($escaneo['id']) ?>" style="margin-left:12px">Cancelar</a>
    </form>
</section>
