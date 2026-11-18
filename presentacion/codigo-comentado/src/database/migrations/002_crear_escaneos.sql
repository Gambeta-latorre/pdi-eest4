-- ============================================================================
--  002: tabla de escaneos (entidad principal). La FK se agrega en la 003.
-- ============================================================================
--  Cada fila = un escaneo hecho a una IP en un momento.
--  Después se le sumaron columnas/cambios con otras migraciones:
--    006 -> columna "notas"      007 -> usuario_id pasa a ser opcional (NULL)
-- ============================================================================
CREATE TABLE escaneos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- A qué usuario pertenece (hoy queda NULL porque no hay login).
    usuario_id INT NOT NULL,
    -- La IP escaneada. 45 caracteres alcanza incluso para una IPv6.
    objetivo VARCHAR(45) NOT NULL,
    fecha DATETIME NOT NULL,
    -- El JSON completo que devolvió el escáner de Python (respaldo).
    -- TEXT = texto largo (hasta 64 KB).
    resultados TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
