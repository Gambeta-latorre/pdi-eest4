<section class="tarjeta">
    <h1>¿Qué puertos tiene abiertos tu red?</h1>
    <p>TraffiCop ayuda a pequeños negocios y hogares a ver qué puertos tiene abiertos su red
       y qué riesgo implica cada uno, con una explicación simple.</p>
    <a class="boton" href="/escaneos/create">Escanear un equipo</a>
</section>

<section class="tarjeta">
    <h2>Ejemplo de resultado</h2>
    <p class="suave">Datos de ejemplo (no es un escaneo real).</p>
    <table>
        <tr><th>Puerto</th><th>Servicio</th><th>Riesgo</th><th>¿Qué significa?</th></tr>
        <?php foreach ($ejemplo as $p): ?>
            <tr>
                <td><?= e($p['puerto']) ?></td>
                <td><?= e($p['servicio']) ?></td>
                <td><span class="riesgo riesgo-<?= e($p['riesgo']) ?>"><?= e($p['riesgo']) ?></span></td>
                <td><?= e($p['explicacion']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
