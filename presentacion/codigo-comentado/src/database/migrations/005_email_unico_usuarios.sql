-- ============================================================================
--  005: no puede haber dos usuarios con el mismo email.
-- ============================================================================
--  UNIQUE = restricción de unicidad: la base rechaza un INSERT con un email
--  repetido, aunque el código PHP se olvide de controlarlo.
-- ============================================================================
ALTER TABLE usuarios ADD CONSTRAINT uq_usuarios_email UNIQUE (email);
