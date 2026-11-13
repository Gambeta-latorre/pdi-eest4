-- 006 (bonus): modificar una tabla existente agregando la columna "notas".
ALTER TABLE escaneos ADD COLUMN notas VARCHAR(255) NULL AFTER fecha;
