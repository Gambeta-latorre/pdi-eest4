-- 003: relación escaneos -> usuarios. Si se borra un usuario, se borran sus escaneos.
ALTER TABLE escaneos
    ADD CONSTRAINT fk_escaneos_usuarios
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE CASCADE;
