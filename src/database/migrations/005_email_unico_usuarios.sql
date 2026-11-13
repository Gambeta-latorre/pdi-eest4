-- 005: no puede haber dos usuarios con el mismo email.
ALTER TABLE usuarios ADD CONSTRAINT uq_usuarios_email UNIQUE (email);
