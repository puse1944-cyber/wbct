-- =============================================
-- SOLUCIÓN PARA ERRORES DE CLAVE FORÁNEA
-- =============================================
-- Ejecutar este script si encuentras errores de restricción de clave foránea

-- 1. Deshabilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Eliminar todas las tablas en orden inverso (para limpiar completamente)
DROP TABLE IF EXISTS `breathe_system_config`;
DROP TABLE IF EXISTS `login_monitor_logs`;
DROP TABLE IF EXISTS `breathe_sessions`;
DROP TABLE IF EXISTS `breathe_credit_transactions`;
DROP TABLE IF EXISTS `breathe_gates`;
DROP TABLE IF EXISTS `breathe_telegram_users`;
DROP TABLE IF EXISTS `breathe_telegram_config`;
DROP TABLE IF EXISTS `breathe_logs`;
DROP TABLE IF EXISTS `breathe_news`;
DROP TABLE IF EXISTS `breathe_subscriptions`;
DROP TABLE IF EXISTS `breathe_lives`;
DROP TABLE IF EXISTS `breathe_keys`;
DROP TABLE IF EXISTS `breathe_users`;

-- 3. Eliminar vistas si existen
DROP VIEW IF EXISTS `v_user_stats`;
DROP VIEW IF EXISTS `v_gate_stats`;

-- 4. Eliminar procedimientos almacenados si existen
DROP PROCEDURE IF EXISTS `sp_cleanup_expired_sessions`;
DROP PROCEDURE IF EXISTS `sp_get_system_stats`;

-- 5. Eliminar triggers si existen
DROP TRIGGER IF EXISTS `tr_credit_usage`;

-- 6. Rehabilitar verificación de claves foráneas
SET FOREIGN_KEY_CHECKS = 1;

-- 7. Mensaje de confirmación
SELECT 'Todas las tablas han sido eliminadas. Ahora puedes ejecutar database_hosting_compatible.sql sin errores.' as mensaje;
