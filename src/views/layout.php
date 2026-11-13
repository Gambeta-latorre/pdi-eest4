<?php $usuarioActual = $_SESSION['usuario'] ?? null; ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'TraffiCop') ?> · TraffiCop</title>
    <style>
        :root { --fondo:#0f172a; --tarjeta:#1e293b; --texto:#e2e8f0; --suave:#94a3b8; --acento:#38bdf8;
                --alto:#ef4444; --medio:#f59e0b; --bajo:#22c55e; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, sans-serif; background:var(--fondo); color:var(--texto); }
        header { display:flex; justify-content:space-between; align-items:center; padding:14px 24px; background:#020617; }
        header a { color:var(--texto); text-decoration:none; margin-left:16px; }
        .logo { font-weight:700; font-size:1.2rem; color:var(--acento); margin:0; }
        main { max-width:900px; margin:0 auto; padding:24px 16px; }
        .tarjeta { background:var(--tarjeta); border-radius:10px; padding:20px; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:8px; border-bottom:1px solid #334155; }
        a { color:var(--acento); }
        label { display:block; margin:12px 0 4px; }
        input, textarea { width:100%; padding:10px; border-radius:6px; border:1px solid #475569; background:#0b1222; color:var(--texto); font:inherit; }
        button, .boton { display:inline-block; margin-top:14px; padding:10px 16px; border:0; border-radius:6px; background:var(--acento);
                 color:#0f172a; font-weight:600; cursor:pointer; text-decoration:none; font:600 1rem system-ui, sans-serif; }
        .peligro { background:var(--alto); color:#fff; }
        .error { background:#7f1d1d; padding:10px; border-radius:6px; }
        .riesgo { padding:2px 10px; border-radius:20px; font-weight:600; color:#0f172a; }
        .riesgo-alto { background:var(--alto); color:#fff; } .riesgo-medio { background:var(--medio); } .riesgo-bajo { background:var(--bajo); }
        .suave { color:var(--suave); }
        .acciones { display:flex; gap:10px; align-items:center; }
        .acciones form { margin:0; }
    </style>
</head>
<body>
<header>
    <a href="/" class="logo">🚦 TraffiCop</a>
    <nav>
        <?php if ($usuarioActual): ?>
            <span class="suave">Hola, <?= e($usuarioActual['nombre']) ?></span>
            <a href="/escaneos">Mis escaneos</a>
            <a href="/escaneos/create">Nuevo escaneo</a>
            <a href="/auth/logout">Salir</a>
        <?php else: ?>
            <a href="/auth/login">Iniciar sesión</a>
            <a href="/auth/register">Crear cuenta</a>
        <?php endif; ?>
    </nav>
</header>
<main>
    <?= $content ?>
</main>
</body>
</html>
