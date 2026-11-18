-- ============================================================================
--  006 (bonus): modificar una tabla existente agregando la columna "notas".
-- ============================================================================
--  ALTER TABLE ... ADD COLUMN = agregar una columna sin perder los datos.
--  NULL = opcional (un escaneo puede no tener notas).
--  VARCHAR(255) -> por eso el controlador valida un máximo de 255 caracteres.
--  AFTER fecha = la ubica después de la columna "fecha" (solo orden visual).
-- ============================================================================
ALTER TABLE escaneos ADD COLUMN notas VARCHAR(255) NULL AFTER fecha;
