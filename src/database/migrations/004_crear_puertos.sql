-- 004: puertos abiertos de cada escaneo. Si se borra el escaneo, se borran sus puertos.
CREATE TABLE puertos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    escaneo_id INT NOT NULL,
    puerto INT NOT NULL,
    servicio VARCHAR(50) NOT NULL,
    riesgo ENUM('alto', 'medio', 'bajo') NOT NULL,
    CONSTRAINT fk_puertos_escaneos
        FOREIGN KEY (escaneo_id) REFERENCES escaneos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
