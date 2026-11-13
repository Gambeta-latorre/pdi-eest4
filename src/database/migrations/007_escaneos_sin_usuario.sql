-- 007: la app ya no tiene login, así que un escaneo puede no tener usuario.
-- La tabla usuarios y la FK se mantienen por si en el futuro se vuelve a agregar.
ALTER TABLE escaneos MODIFY usuario_id INT NULL;
