<section class="tarjeta">
    <h1>Nuevo escaneo</h1>
    <p class="suave">Solo se permiten equipos de tu red local (10.x.x.x, 172.16–31.x.x, 192.168.x.x) o tu propia PC (127.0.0.1).</p>
    <?php if (!empty($error)): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="/escaneos">
        <label for="objetivo">IP del equipo</label>
        <input id="objetivo" name="objetivo" placeholder="192.168.0.1" required value="<?= e($viejo['objetivo'] ?? '') ?>">
        <label for="notas">Notas (opcional, máximo 255 caracteres)</label>
        <textarea id="notas" name="notas" rows="3" maxlength="255"><?= e($viejo['notas'] ?? '') ?></textarea>
        <button type="submit">Escanear</button>
    </form>
</section>
