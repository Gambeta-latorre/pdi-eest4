-- ============================================================================
--  003: relación escaneos -> usuarios. Si se borra un usuario, se borran sus escaneos.
-- ============================================================================
--  CLAVE FORÁNEA (FOREIGN KEY): asegura que escaneos.usuario_id apunte a un
--  usuario que EXISTE (integridad referencial). Relación 1 a N:
--  un usuario puede tener muchos escaneos.
--  ON DELETE CASCADE: al borrar el usuario, MySQL borra solo sus escaneos.
--  Va en una migración aparte para mostrar cómo se MODIFICA una tabla existente.
-- ============================================================================
ALTER TABLE escaneos
    ADD CONSTRAINT fk_escaneos_usuarios          -- nombre de la restricción
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE CASCADE;
