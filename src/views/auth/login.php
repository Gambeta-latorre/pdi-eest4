<section class="tarjeta" style="max-width:420px;margin:40px auto">
    <h1>Iniciar sesión</h1>
    <?php if (!empty($error)): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <form method="post" action="/auth/login">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" required value="<?= e($viejo['email'] ?? '') ?>">
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" required>
        <button type="submit">Entrar</button>
    </form>
    <p class="suave">¿No tenés cuenta? <a href="/auth/register">Creá una</a></p>
</section>
