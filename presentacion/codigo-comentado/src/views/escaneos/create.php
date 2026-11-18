<?php
/*
 * ============================================================================
 *  src/views/escaneos/create.php  ->  FORMULARIO "NUEVO ESCANEO"
 * ============================================================================
 *  Lo muestran:
 *    - create() la primera vez (vacío).
 *    - store() si hubo un error (status 422), con $error y $viejo
 *      (lo que el usuario había escrito, para que no lo tenga que reescribir).
 * ============================================================================
 */
?>
<section class="tarjeta">
    <h1>Nuevo escaneo</h1>
    <p class="suave">Solo se permiten equipos de tu red local (10.x.x.x, 172.16–31.x.x, 192.168.x.x) o tu propia PC (127.0.0.1).</p>
    <!-- Si el controlador mandó un error, lo mostramos en un recuadro rojo. -->
    <?php if (!empty($error)): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
    <!-- method="post" + action="/escaneos" -> ruta POST /escaneos -> store(). -->
    <form method="post" action="/escaneos">
        <!-- for="objetivo" une la etiqueta con el campo (accesibilidad). -->
        <label for="objetivo">IP del equipo</label>
        <!-- name="objetivo" es la clave con la que llega al controlador.
             required = validación del navegador (la real es la del servidor).
             value = lo que había escrito antes, si hubo error (?? '' si no). -->
        <input id="objetivo" name="objetivo" placeholder="192.168.0.1" required value="<?= e($viejo['objetivo'] ?? '') ?>">
        <label for="notas">Notas (opcional, máximo 255 caracteres)</label>
        <!-- maxlength="255" ayuda al usuario; el servidor igual vuelve a controlar. -->
        <textarea id="notas" name="notas" rows="3" maxlength="255"><?= e($viejo['notas'] ?? '') ?></textarea>
        <button type="submit">Escanear</button>
    </form>
</section>
