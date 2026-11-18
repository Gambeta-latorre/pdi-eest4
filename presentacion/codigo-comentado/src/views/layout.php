<?php
/*
 * ============================================================================
 *  src/views/layout.php  ->  PLANTILLA MADRE (layout)
 * ============================================================================
 *  Todas las páginas comparten este HTML: <head>, estilos, encabezado y menú.
 *  Cada vista (index.php, show.php...) se genera primero y su HTML se mete
 *  acá adentro en la variable $content. Así no repetimos el encabezado en
 *  cada archivo (se configura en bootstrap.php con setLayout).
 *
 *  Sintaxis de las vistas:
 *    <?= ... ?>  es lo mismo que <?php echo ...; ?>  (imprimir algo).
 * ============================================================================
 */
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <!-- Para que se vea bien en el celular (diseño responsive). -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- $titulo lo manda el controlador. Si no vino, usamos 'TraffiCop'.
         Pasa por e() para evitar XSS. -->
    <title><?= e($titulo ?? 'TraffiCop') ?> · TraffiCop</title>
    <!-- CSS dentro del HTML: la app es chica y así no hay archivos extra. -->
    <style>
        /* Variables CSS: colores definidos una vez y reutilizados.
           --alto/--medio/--bajo = colores del "semáforo" de riesgo. */
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
        /* Etiqueta de riesgo: la clase se arma según el valor,
           ej: class="riesgo riesgo-alto" -> fondo rojo. */
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
    <!-- Menú: links a las rutas GET del CRUD. -->
    <nav>
        <a href="/escaneos">Historial</a>
        <a href="/escaneos/create">Nuevo escaneo</a>
    </nav>
</header>
<main>
    <!-- Acá se inserta el HTML de la vista de cada página.
         NO usa e() a propósito: $content ya es HTML generado por nosotros
         (y dentro de cada vista los datos ya se escaparon con e()). -->
    <?= $content ?>
</main>
</body>
</html>
