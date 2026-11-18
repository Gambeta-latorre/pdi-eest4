-- ============================================================================
--  007: la app ya no tiene login, así que un escaneo puede no tener usuario.
--  La tabla usuarios y la FK se mantienen por si en el futuro se vuelve a agregar.
-- ============================================================================
--  MODIFY cambia la definición de una columna: usuario_id pasa de NOT NULL a
--  NULL (opcional). La clave foránea igual funciona: un NULL no apunta a nadie
--  y es válido; si algún día vuelve el login, ya está todo preparado.
-- ============================================================================
ALTER TABLE escaneos MODIFY usuario_id INT NULL;
