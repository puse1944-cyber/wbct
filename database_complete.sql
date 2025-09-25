-- =============================================
-- DARK CT - DATABASE COMPLETE SCHEMA
-- =============================================
-- Este archivo contiene todas las tablas necesarias para el sistema Dark CT
-- Incluye: usuarios, keys, lives, suscripciones, logs, y sistema híbrido

-- =============================================
-- 1. TABLA DE USUARIOS
-- =============================================
DROP TABLE IF EXISTS `breathe_users`;
CREATE TABLE `breathe_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `key_breathe` varchar(255) DEFAULT NULL,
  `suscripcion` int(11) NOT NULL DEFAULT 1,
  `creditos` int(11) NOT NULL DEFAULT 0,
  `fech_reg` date NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_suscripcion` (`suscripcion`),
  KEY `idx_active` (`active`),
  KEY `idx_key_breathe` (`key_breathe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. TABLA DE KEYS
-- =============================================
DROP TABLE IF EXISTS `breathe_keys`;
CREATE TABLE `breathe_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number_key` varchar(255) NOT NULL,
  `credits` int(11) NOT NULL DEFAULT 0,
  `dias` int(11) NOT NULL DEFAULT 30,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `username` varchar(255) DEFAULT NULL,
  `fecha_reg` date NOT NULL,
  `fecha_inicio` date NOT NULL,
  `suscripcion` varchar(50) NOT NULL DEFAULT '1',
  `used_by` int(11) DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number_key` (`number_key`),
  KEY `idx_active` (`active`),
  KEY `idx_username` (`username`),
  KEY `idx_fecha_reg` (`fecha_reg`),
  KEY `idx_used_by` (`used_by`),
  FOREIGN KEY (`used_by`) REFERENCES `breathe_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. TABLA DE LIVES (TARJETAS VÁLIDAS)
-- =============================================
DROP TABLE IF EXISTS `breathe_lives`;
CREATE TABLE `breathe_lives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gate_type` varchar(50) NOT NULL,
  `card_number` varchar(20) NOT NULL,
  `card_type` varchar(20) DEFAULT NULL,
  `card_brand` varchar(20) DEFAULT NULL,
  `country` varchar(10) DEFAULT NULL,
  `bin` varchar(10) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `level` varchar(20) DEFAULT NULL,
  `response` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_gate_type` (`gate_type`),
  KEY `idx_card_number` (`card_number`),
  KEY `idx_country` (`country`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. TABLA DE SUSCRIPCIONES
-- =============================================
DROP TABLE IF EXISTS `breathe_subscriptions`;
CREATE TABLE `breathe_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subscription_type` int(11) NOT NULL DEFAULT 1,
  `credits_included` int(11) NOT NULL DEFAULT 0,
  `credits_used` int(11) NOT NULL DEFAULT 0,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `auto_renew` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_subscription_type` (`subscription_type`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_end_date` (`end_date`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. TABLA DE NOTICIAS
-- =============================================
DROP TABLE IF EXISTS `breathe_news`;
CREATE TABLE `breathe_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_is_published` (`is_published`),
  KEY `idx_priority` (`priority`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. TABLA DE LOGS DEL SISTEMA
-- =============================================
DROP TABLE IF EXISTS `breathe_logs`;
CREATE TABLE `breathe_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `data` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. TABLA DE CONFIGURACIÓN DEL BOT DE TELEGRAM
-- =============================================
DROP TABLE IF EXISTS `breathe_telegram_config`;
CREATE TABLE `breathe_telegram_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bot_token` varchar(255) NOT NULL,
  `webhook_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notify_lives` tinyint(1) NOT NULL DEFAULT 1,
  `notify_errors` tinyint(1) NOT NULL DEFAULT 1,
  `admin_chat_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 8. TABLA DE USUARIOS REGISTRADOS EN TELEGRAM
-- =============================================
DROP TABLE IF EXISTS `breathe_telegram_users`;
CREATE TABLE `breathe_telegram_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `telegram_id` varchar(50) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notify_lives` tinyint(1) NOT NULL DEFAULT 1,
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `telegram_id` (`telegram_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_is_active` (`is_active`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 9. TABLA DE GATES (CONFIGURACIÓN)
-- =============================================
DROP TABLE IF EXISTS `breathe_gates`;
CREATE TABLE `breathe_gates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `api_url` varchar(500) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `credits_cost` int(11) NOT NULL DEFAULT 1,
  `countries` json DEFAULT NULL,
  `config` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_type` (`type`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 10. TABLA DE TRANSACCIONES DE CRÉDITOS
-- =============================================
DROP TABLE IF EXISTS `breathe_credit_transactions`;
CREATE TABLE `breathe_credit_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `transaction_type` enum('add','subtract','refund','expired') NOT NULL,
  `amount` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `gate_type` varchar(50) DEFAULT NULL,
  `reference_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_transaction_type` (`transaction_type`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 11. TABLA DE SESIONES
-- =============================================
DROP TABLE IF EXISTS `breathe_sessions`;
CREATE TABLE `breathe_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `data` blob,
  `last_activity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_last_activity` (`last_activity`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 12. TABLA DE CONFIGURACIÓN DEL SISTEMA
-- =============================================
DROP TABLE IF EXISTS `breathe_system_config`;
CREATE TABLE `breathe_system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL,
  `config_value` text,
  `config_type` enum('string','integer','boolean','json') NOT NULL DEFAULT 'string',
  `description` text,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key` (`config_key`),
  KEY `idx_is_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- INSERTAR DATOS INICIALES
-- =============================================

-- Usuario administrador por defecto
INSERT INTO `breathe_users` (`email`, `username`, `password`, `suscripcion`, `creditos`, `fech_reg`, `active`) VALUES
('admin@dark-ct.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 999999, CURDATE(), 1);

-- Configuración del sistema
INSERT INTO `breathe_system_config` (`config_key`, `config_value`, `config_type`, `description`, `is_public`) VALUES
('site_name', 'Dark CT', 'string', 'Nombre del sitio', 1),
('site_description', 'Sistema de Gates y Webhooks de Telegram', 'string', 'Descripción del sitio', 1),
('maintenance_mode', '0', 'boolean', 'Modo mantenimiento', 0),
('max_credits_per_user', '10000', 'integer', 'Máximo de créditos por usuario', 0),
('default_credits_new_user', '10', 'integer', 'Créditos por defecto para nuevos usuarios', 0),
('telegram_notifications', '1', 'boolean', 'Notificaciones de Telegram habilitadas', 0);

-- Gates por defecto
INSERT INTO `breathe_gates` (`name`, `type`, `is_active`, `credits_cost`, `countries`) VALUES
('Amazon Gate', 'amazon', 1, 2, '["US", "CA", "UK", "DE", "FR", "IT", "ES", "JP"]'),
('Chase Gate', 'chase', 1, 2, '["US"]'),
('PayPal Gate', 'paypal', 1, 2, '["US", "CA", "UK", "DE", "FR", "IT", "ES"]'),
('Stripe Gate', 'stripe', 1, 2, '["US", "CA", "UK", "DE", "FR", "IT", "ES"]');

-- Noticia de bienvenida
INSERT INTO `breathe_news` (`title`, `content`, `author`, `is_published`, `priority`) VALUES
('¡Bienvenido a Dark CT!', 'Sistema de gates y webhooks de Telegram completamente funcional. Disfruta de todas las características premium.', 'Sistema', 1, 1);

-- =============================================
-- CREAR VISTAS ÚTILES
-- =============================================

-- Vista de usuarios con información de suscripción
CREATE OR REPLACE VIEW `v_users_with_subscription` AS
SELECT 
    u.id,
    u.email,
    u.username,
    u.suscripcion,
    u.creditos,
    u.active,
    u.fech_reg,
    u.last_login,
    s.subscription_type,
    s.end_date as subscription_end,
    s.is_active as subscription_active,
    COALESCE(ct.total_credits_used, 0) as total_credits_used
FROM breathe_users u
LEFT JOIN breathe_subscriptions s ON u.id = s.user_id AND s.is_active = 1
LEFT JOIN (
    SELECT user_id, SUM(amount) as total_credits_used 
    FROM breathe_credit_transactions 
    WHERE transaction_type = 'subtract' 
    GROUP BY user_id
) ct ON u.id = ct.user_id;

-- Vista de estadísticas de lives
CREATE OR REPLACE VIEW `v_lives_stats` AS
SELECT 
    gate_type,
    COUNT(*) as total_lives,
    COUNT(DISTINCT user_id) as unique_users,
    DATE(created_at) as live_date,
    country,
    COUNT(*) as lives_by_country
FROM breathe_lives 
GROUP BY gate_type, DATE(created_at), country;

-- =============================================
-- CREAR PROCEDIMIENTOS ALMACENADOS
-- =============================================

DELIMITER //

-- Procedimiento para agregar créditos a un usuario
CREATE PROCEDURE AddCreditsToUser(
    IN p_user_id INT,
    IN p_amount INT,
    IN p_reason VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Actualizar créditos del usuario
    UPDATE breathe_users 
    SET creditos = creditos + p_amount 
    WHERE id = p_user_id;
    
    -- Registrar transacción
    INSERT INTO breathe_credit_transactions (user_id, transaction_type, amount, reason)
    VALUES (p_user_id, 'add', p_amount, p_reason);
    
    COMMIT;
END //

-- Procedimiento para usar créditos
CREATE PROCEDURE UseCredits(
    IN p_user_id INT,
    IN p_amount INT,
    IN p_gate_type VARCHAR(50),
    IN p_reference_id VARCHAR(100)
)
BEGIN
    DECLARE current_credits INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- Verificar créditos disponibles
    SELECT creditos INTO current_credits FROM breathe_users WHERE id = p_user_id;
    
    IF current_credits >= p_amount THEN
        -- Descontar créditos
        UPDATE breathe_users 
        SET creditos = creditos - p_amount 
        WHERE id = p_user_id;
        
        -- Registrar transacción
        INSERT INTO breathe_credit_transactions (user_id, transaction_type, amount, reason, gate_type, reference_id)
        VALUES (p_user_id, 'subtract', p_amount, CONCAT('Gate: ', p_gate_type), p_gate_type, p_reference_id);
        
        SELECT 1 as success, 'Credits used successfully' as message;
    ELSE
        SELECT 0 as success, 'Insufficient credits' as message;
    END IF;
    
    COMMIT;
END //

-- Procedimiento para limpiar sesiones expiradas
CREATE PROCEDURE CleanExpiredSessions()
BEGIN
    DELETE FROM breathe_sessions 
    WHERE last_activity < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY));
END //

DELIMITER ;

-- =============================================
-- CREAR TRIGGERS
-- =============================================

-- Trigger para actualizar créditos cuando se usa una key
DELIMITER //
CREATE TRIGGER tr_key_used AFTER UPDATE ON breathe_keys
FOR EACH ROW
BEGIN
    IF NEW.used_by IS NOT NULL AND OLD.used_by IS NULL THEN
        -- Agregar créditos al usuario
        UPDATE breathe_users 
        SET creditos = creditos + NEW.credits 
        WHERE id = NEW.used_by;
        
        -- Registrar transacción
        INSERT INTO breathe_credit_transactions (user_id, transaction_type, amount, reason, reference_id)
        VALUES (NEW.used_by, 'add', NEW.credits, 'Key activation', NEW.number_key);
    END IF;
END //
DELIMITER ;

-- =============================================
-- CREAR ÍNDICES ADICIONALES PARA RENDIMIENTO
-- =============================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_lives_user_gate ON breathe_lives(user_id, gate_type);
CREATE INDEX idx_lives_created_country ON breathe_lives(created_at, country);
CREATE INDEX idx_credits_user_type ON breathe_credit_transactions(user_id, transaction_type);
CREATE INDEX idx_sessions_user_activity ON breathe_sessions(user_id, last_activity);

-- =============================================
-- FINALIZAR
-- =============================================

-- Mostrar resumen de tablas creadas
SELECT 
    TABLE_NAME as 'Tabla',
    TABLE_ROWS as 'Filas',
    DATA_LENGTH as 'Tamaño (bytes)',
    CREATE_TIME as 'Creada'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME LIKE 'breathe_%'
ORDER BY TABLE_NAME;
