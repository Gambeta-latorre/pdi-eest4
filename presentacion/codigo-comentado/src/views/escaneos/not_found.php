<?php
/*
 * ============================================================================
 *  src/views/escaneos/not_found.php  ->  PÁGINA 404 "NO ENCONTRADO"
 * ============================================================================
 *  La muestra EscaneoController::noEncontrado() con status 404 cuando el id
 *  pedido no existe (por ejemplo /escaneos/9999 o uno ya borrado).
 *  Una página amigable es mejor que un error técnico.
 * ============================================================================
 */
?>
<section class="tarjeta">
    <h1>Escaneo no encontrado</h1>
    <p>El escaneo que buscás no existe o ya fue borrado.</p>
    <a class="boton" href="/escaneos">Volver al historial</a>
</section>
