<section class="tarjeta" style="max-width:420px;margin:40px auto">
    <h1>Crear cuenta</h1>
    <?php if (!empty($error)): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="/auth/register">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" required value="<?= e($viejo['nombre'] ?? '') ?>">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?= e($viejo['email'] ?? '') ?>">
        <label for="password">Contraseña (mínimo 8 caracteres)</label>
        <input id="password" name="password" type="password" minlength="8" required>
        <button type="submit">Registrarme</button>
    </form>
    <p class="suave">¿Ya tenés cuenta? <a href="/auth/login">Iniciá sesión</a></p>
</section>
