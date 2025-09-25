-- =============================================
-- BASE DE DATOS DARK CT - VERSIÓN HOSTING COMPATIBLE
-- Sin referencias a information_schema
-- =============================================

-- Configurar charset y collation
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- 1. TABLA DE USUARIOS
-- =============================================
DROP TABLE IF EXISTS `breathe_users`;
CREATE TABLE `breathe_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `breathe_password` varchar(255) NOT NULL,
  `creditos` int(11) NOT NULL DEFAULT 0,
  `suscripcion` int(11) NOT NULL DEFAULT 1,
  `fech_reg` date DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_suscripcion` (`suscripcion`),
  KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. TABLA DE CLAVES
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `number_key` (`number_key`),
  KEY `idx_active` (`active`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. TABLA DE LIVES
-- =============================================
DROP TABLE IF EXISTS `breathe_lives`;
CREATE TABLE `breathe_lives` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `card_number` varchar(20) NOT NULL,
  `card_type` varchar(50) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `gate` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'live',
  `amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `response_data` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_gate` (`gate`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. TABLA DE SUSCRIPCIONES
-- =============================================
DROP TABLE IF EXISTS `breathe_subscriptions`;
CREATE TABLE `breathe_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subscription_type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_is_active` (`is_active`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. TABLA DE LOGS
-- =============================================
DROP TABLE IF EXISTS `breathe_logs`;
CREATE TABLE `breathe_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. TABLA DE CONFIGURACIÓN DE TELEGRAM
-- =============================================
DROP TABLE IF EXISTS `breathe_telegram_config`;
CREATE TABLE `breathe_telegram_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bot_token` varchar(255) NOT NULL,
  `admin_chat_id` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `webhook_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 8. TABLA DE USUARIOS DE TELEGRAM
-- =============================================
DROP TABLE IF EXISTS `breathe_telegram_users`;
CREATE TABLE `breathe_telegram_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telegram_id` bigint(20) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `is_bot` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `telegram_id` (`telegram_id`),
  KEY `idx_username` (`username`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 9. TABLA DE GATES
-- =============================================
DROP TABLE IF EXISTS `breathe_gates`;
CREATE TABLE `breathe_gates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `credits_required` int(11) NOT NULL DEFAULT 2,
  `api_endpoint` varchar(500) DEFAULT NULL,
  `config` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 10. TABLA DE TRANSACCIONES DE CRÉDITOS
-- =============================================
DROP TABLE IF EXISTS `breathe_credit_transactions`;
CREATE TABLE `breathe_credit_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `transaction_type` enum('add','subtract','gate_usage','admin_adjustment') NOT NULL,
  `amount` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `gate_used` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_transaction_type` (`transaction_type`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 11. TABLA DE SESIONES
-- =============================================
DROP TABLE IF EXISTS `breathe_sessions`;
CREATE TABLE `breathe_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token` (`session_token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_last_activity` (`last_activity`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 12. TABLA DE LOGS DE LOGIN MONITOR
-- =============================================
DROP TABLE IF EXISTS `login_monitor_logs`;
CREATE TABLE `login_monitor_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `user_agent` text,
  `location` varchar(255) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `referer` varchar(500) DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `suspicious_activity` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_ip` (`ip`),
  KEY `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `breathe_users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 13. TABLA DE CONFIGURACIÓN DEL SISTEMA
-- =============================================
DROP TABLE IF EXISTS `breathe_system_config`;
CREATE TABLE `breathe_system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL,
  `config_value` text,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_key` (`config_key`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DATOS INICIALES
-- =============================================

-- Insertar noticia de bienvenida
INSERT INTO `breathe_news` (`title`, `content`, `author`, `is_published`, `priority`) VALUES
('¡Bienvenido a Dark CT!', 'Sistema de gates y webhooks de Telegram completamente funcional. Disfruta de todas las características premium.', 'Sistema', 1, 1);

-- Insertar gates por defecto
INSERT INTO `breathe_gates` (`name`, `display_name`, `description`, `is_active`, `credits_required`) VALUES
('amazon', 'Amazon Gate', 'Verificación de tarjetas para Amazon', 1, 2),
('chase', 'Chase Gate', 'Verificación de tarjetas para Chase', 1, 2),
('paypal', 'PayPal Gate', 'Verificación de tarjetas para PayPal', 1, 2),
('stripe', 'Stripe Gate', 'Verificación de tarjetas para Stripe', 1, 2),
('stripezoura', 'Stripe Zoura Gate', 'Verificación de tarjetas para Stripe Zoura', 1, 2),
('miracle', 'Miracle Gate', 'Verificación de tarjetas para Miracle', 1, 2),
('mirror', 'Mirror Gate', 'Verificación de tarjetas para Mirror', 1, 2),
('mystric', 'Mystric Gate', 'Verificación de tarjetas para Mystric', 1, 2);

-- Insertar configuración del sistema
INSERT INTO `breathe_system_config` (`config_key`, `config_value`, `description`, `is_active`) VALUES
('site_name', 'Dark CT', 'Nombre del sitio web', 1),
('site_description', 'Sistema de verificación de tarjetas', 'Descripción del sitio', 1),
('maintenance_mode', '0', 'Modo de mantenimiento (0=desactivado, 1=activado)', 1),
('max_login_attempts', '5', 'Máximo número de intentos de login', 1),
('session_timeout', '3600', 'Tiempo de expiración de sesión en segundos', 1);

-- Restaurar foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =============================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX `idx_users_suscripcion_active` ON `breathe_users` (`suscripcion`, `active`);
CREATE INDEX `idx_lives_user_gate` ON `breathe_lives` (`user_id`, `gate`);
CREATE INDEX `idx_lives_status_created` ON `breathe_lives` (`status`, `created_at`);
CREATE INDEX `idx_logs_user_action` ON `breathe_logs` (`user_id`, `action`);
CREATE INDEX `idx_credit_transactions_user_type` ON `breathe_credit_transactions` (`user_id`, `transaction_type`);

-- =============================================
-- TRIGGERS PARA AUDITORÍA
-- =============================================

-- Trigger para actualizar créditos cuando se usa un gate
DELIMITER $$
CREATE TRIGGER `tr_credit_usage` AFTER INSERT ON `breathe_lives`
FOR EACH ROW
BEGIN
    IF NEW.status = 'live' THEN
        -- Restar créditos del usuario
        UPDATE `breathe_users` 
        SET `creditos` = `creditos` - 2 
        WHERE `id` = NEW.user_id;
        
        -- Registrar transacción
        INSERT INTO `breathe_credit_transactions` 
        (`user_id`, `transaction_type`, `amount`, `description`, `gate_used`) 
        VALUES 
        (NEW.user_id, 'gate_usage', -2, CONCAT('Uso de gate: ', NEW.gate), NEW.gate);
    END IF;
END$$
DELIMITER ;

-- =============================================
-- VISTAS ÚTILES
-- =============================================

-- Vista de estadísticas de usuarios
CREATE VIEW `v_user_stats` AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.creditos,
    u.suscripcion,
    u.active,
    COUNT(l.id) as total_lives,
    COUNT(CASE WHEN l.status = 'live' THEN 1 END) as successful_lives,
    MAX(l.created_at) as last_live_date
FROM `breathe_users` u
LEFT JOIN `breathe_lives` l ON u.id = l.user_id
GROUP BY u.id, u.username, u.email, u.creditos, u.suscripcion, u.active;

-- Vista de estadísticas de gates
CREATE VIEW `v_gate_stats` AS
SELECT 
    g.name,
    g.display_name,
    g.is_active,
    COUNT(l.id) as total_uses,
    COUNT(CASE WHEN l.status = 'live' THEN 1 END) as successful_uses,
    ROUND(COUNT(CASE WHEN l.status = 'live' THEN 1 END) * 100.0 / COUNT(l.id), 2) as success_rate
FROM `breathe_gates` g
LEFT JOIN `breathe_lives` l ON g.name = l.gate
GROUP BY g.name, g.display_name, g.is_active;

-- =============================================
-- PROCEDIMIENTOS ALMACENADOS
-- =============================================

-- Procedimiento para limpiar sesiones expiradas
DELIMITER $$
CREATE PROCEDURE `sp_cleanup_expired_sessions`()
BEGIN
    DELETE FROM `breathe_sessions` 
    WHERE `last_activity` < DATE_SUB(NOW(), INTERVAL 24 HOUR);
END$$
DELIMITER ;

-- Procedimiento para obtener estadísticas del sistema
DELIMITER $$
CREATE PROCEDURE `sp_get_system_stats`()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM `breathe_users` WHERE `active` = 1) as active_users,
        (SELECT COUNT(*) FROM `breathe_lives` WHERE `status` = 'live') as total_lives,
        (SELECT COUNT(*) FROM `breathe_lives` WHERE `created_at` >= CURDATE()) as today_lives,
        (SELECT SUM(`creditos`) FROM `breathe_users`) as total_credits,
        (SELECT COUNT(*) FROM `breathe_gates` WHERE `is_active` = 1) as active_gates;
END$$
DELIMITER ;

-- =============================================
-- SOLUCIÓN PARA ERRORES DE CLAVE FORÁNEA
-- =============================================

-- Si encuentras errores de restricción de clave foránea, ejecuta estos comandos:

-- 1. Deshabilitar verificación de claves foráneas temporalmente
-- SET FOREIGN_KEY_CHECKS = 0;

-- 2. Eliminar todas las tablas en orden inverso (si es necesario)
-- DROP TABLE IF EXISTS `breathe_system_config`;
-- DROP TABLE IF EXISTS `login_monitor_logs`;
-- DROP TABLE IF EXISTS `breathe_sessions`;
-- DROP TABLE IF EXISTS `breathe_credit_transactions`;
-- DROP TABLE IF EXISTS `breathe_gates`;
-- DROP TABLE IF EXISTS `breathe_telegram_users`;
-- DROP TABLE IF EXISTS `breathe_telegram_config`;
-- DROP TABLE IF EXISTS `breathe_logs`;
-- DROP TABLE IF EXISTS `breathe_news`;
-- DROP TABLE IF EXISTS `breathe_subscriptions`;
-- DROP TABLE IF EXISTS `breathe_lives`;
-- DROP TABLE IF EXISTS `breathe_keys`;
-- DROP TABLE IF EXISTS `breathe_users`;

-- 3. Rehabilitar verificación de claves foráneas
-- SET FOREIGN_KEY_CHECKS = 1;

-- 4. Luego ejecutar este script completo nuevamente

-- =============================================
-- FIN DEL SCRIPT
-- =============================================
