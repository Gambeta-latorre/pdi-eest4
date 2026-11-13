-- 002: tabla de escaneos (entidad principal). La FK se agrega en la 003.
CREATE TABLE escaneos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    objetivo VARCHAR(45) NOT NULL,
    fecha DATETIME NOT NULL,
    resultados TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
