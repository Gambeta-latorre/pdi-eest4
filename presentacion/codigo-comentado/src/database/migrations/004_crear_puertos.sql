-- ============================================================================
--  004: puertos abiertos de cada escaneo. Si se borra el escaneo, se borran sus puertos.
-- ============================================================================
--  Relación 1 a N: UN escaneo tiene MUCHOS puertos abiertos.
--  ¿Por qué una tabla aparte y no todo en el JSON? (pregunta típica)
--    Porque así la base está NORMALIZADA: podemos contar puertos con COUNT,
--    ordenarlos, filtrarlos por riesgo, etc., usando SQL.
-- ============================================================================
CREATE TABLE puertos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- A qué escaneo pertenece este puerto.
    escaneo_id INT NOT NULL,
    puerto INT NOT NULL,                 -- ej: 22
    servicio VARCHAR(50) NOT NULL,       -- ej: 'ssh'
    -- ENUM = solo acepta estos 3 valores; cualquier otro da error.
    -- Así la base misma garantiza que el riesgo sea válido.
    riesgo ENUM('alto', 'medio', 'bajo') NOT NULL,
    -- Clave foránea hacia escaneos. ON DELETE CASCADE: al borrar un escaneo,
    -- MySQL borra solos sus puertos (por eso el repositorio hace un solo DELETE).
    CONSTRAINT fk_puertos_escaneos
        FOREIGN KEY (escaneo_id) REFERENCES escaneos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
