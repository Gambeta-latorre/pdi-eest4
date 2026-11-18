-- ============================================================================
--  001: tabla de usuarios
-- ============================================================================
--  Se creó cuando la app tenía login. Hoy NO se usa (los docentes dijeron que
--  el login no era necesario), pero NO se borra: las migraciones nunca se
--  editan ni se eliminan, se agregan nuevas (ver la 007).
-- ============================================================================
CREATE TABLE usuarios (
    -- Clave primaria: número único que MySQL asigna solo (1, 2, 3...).
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- VARCHAR(n) = texto de hasta n caracteres. NOT NULL = obligatorio.
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    -- Nunca se guarda la contraseña: se guarda su HASH (password_hash de PHP),
    -- una "huella" que no se puede revertir. 255 es lo recomendado por PHP.
    password_hash VARCHAR(255) NOT NULL,
    -- Si no se indica, toma la fecha y hora actual.
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
-- InnoDB = motor que soporta claves foráneas y transacciones.
-- utf8mb4 = UTF-8 completo (tildes, ñ, emojis).
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
