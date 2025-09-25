-- =============================================
-- CREAR USUARIO ADMINISTRADOR
-- =============================================
-- Reemplaza los valores entre corchetes con tus datos reales

-- Insertar usuario administrador
INSERT INTO `breathe_users` (
    `username`, 
    `email`, 
    `breathe_password`, 
    `creditos`, 
    `suscripcion`, 
    `fech_reg`, 
    `active`
) VALUES (
    'jxrdan_09',                    -- Cambiar por tu username
    'puse1944@gmail.com',                       -- Cambiar por tu email
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- Password: "password" (cambiar)
    99999,                                  -- Créditos iniciales
    3,                                     -- Suscripción admin (3 = administrador)
    DATE_ADD(CURDATE(), INTERVAL 365 DAY), -- Fecha de expiración (1 año)
    1                                      -- Activo
);

-- Verificar que el usuario se creó correctamente
SELECT 
    id,
    username,
    email,
    creditos,
    suscripcion,
    fech_reg,
    active,
    created_at
FROM `breathe_users` 
WHERE username = 'TU_USERNAME_AQUI';

-- Mensaje de confirmación
SELECT 'Usuario administrador creado exitosamente' as mensaje;
