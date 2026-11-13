<section class="tarjeta">
    <h1>Editar notas del escaneo #<?= e($escaneo['id']) ?></h1>
    <p class="suave">Objetivo: <?= e($escaneo['objetivo']) ?> · <?= e($escaneo['fecha']) ?></p>
    <?php if (!empty($error)): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="/escaneos/<?= e($escaneo['id']) ?>">
        <input type="hidden" name="_METHOD" value="PUT">
        <label for="notas">Notas (máximo 255 caracteres)</label>
        <textarea id="notas" name="notas" rows="4"><?= e($escaneo['notas'] ?? '') ?></textarea>
        <button type="submit">Guardar</button>
        <a href="/escaneos/<?= e($escaneo['id']) ?>" style="margin-left:12px">Cancelar</a>
    </form>
</section>
