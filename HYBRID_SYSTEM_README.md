# 🔄 Sistema Híbrido de Autenticación

## 📋 Descripción

El sistema híbrido permite que los usuarios accedan a los gates de dos maneras:
1. **Por Suscripción de Días** (prioridad alta)
2. **Por Créditos** (sistema de respaldo)

## 🚀 Características

### ✅ Ventajas del Sistema Híbrido
- **Retrocompatibilidad**: No afecta usuarios existentes
- **Flexibilidad**: Múltiples métodos de pago
- **Priorización**: Suscripción tiene prioridad sobre créditos
- **Logging**: Registro completo de accesos
- **Configuración**: Fácil personalización

### 🔧 Funcionamiento

#### 1. Verificación de Acceso
```php
$access_info = check_user_access($connection, $user_id, 2);
```

**Prioridades:**
1. **Suscripción Activa**: Si `fech_reg > fecha_actual` → Acceso permitido
2. **Créditos Suficientes**: Si `creditos >= requeridos` → Acceso permitido
3. **Sin Acceso**: Si ambos fallan → Acceso denegado

#### 2. Deducción de Créditos
```php
$deduction_result = deduct_credits_if_needed($connection, $user_id, $access_info, 2);
```

**Lógica:**
- **Suscripción Activa**: No deduce créditos
- **Solo Créditos**: Deduce los créditos requeridos
- **Logging**: Registra todas las operaciones

## 📁 Archivos del Sistema

### Core Files
- `api/v1.1/core/hybrid_auth.php` - Funciones principales
- `api/v1.1/core/hybrid_config.php` - Configuración
- `test_hybrid_system.php` - Pruebas del sistema

### Gates Actualizados
- `gates/amazon_api.php` - Amazon Gate con sistema híbrido
- `gates/stripe_api.php` - (Pendiente de actualizar)
- `gates/check.php` - (Pendiente de actualizar)
- `gates/chaze_check.php` - (Pendiente de actualizar)

## 🛠️ Implementación

### 1. En Gates Existentes
```php
// Reemplazar verificación de créditos
require_once $path . "/api/v1.1/core/hybrid_auth.php";

$access_info = check_user_access($connection, $user_id, 2);
if (!$access_info['access']) {
    echo json_encode(['status' => 'error', 'message' => $access_info['reason']]);
    exit;
}

// En caso de éxito, deducir créditos si es necesario
$deduction_result = deduct_credits_if_needed($connection, $user_id, $access_info, 2);
```

### 2. En Dashboard
```php
// Mostrar estado híbrido
if (!empty($user_info["fech_reg"])) {
    $expiration_date = new DateTime($user_info["fech_reg"]);
    $current_date = new DateTime();
    if ($current_date <= $expiration_date) {
        echo "Suscripción Activa";
    } else {
        echo "Créditos: " . $user_info["creditos"];
    }
} else {
    echo "Créditos: " . $user_info["creditos"];
}
```

## ⚙️ Configuración

### Parámetros Principales
```php
define('HYBRID_CREDITS_REQUIRED', 2); // Créditos por operación
define('HYBRID_SUBSCRIPTION_PRIORITY', true); // Prioridad de suscripción
define('HYBRID_LOG_ACCESS', true); // Habilitar logging
```

### Tipos de Suscripción
```php
define('HYBRID_SUBSCRIPTION_TYPES', [
    1 => 'Básica',
    2 => 'Premium', 
    3 => 'VIP'
]);
```

### Límites por Suscripción
```php
define('HYBRID_SUBSCRIPTION_LIMITS', [
    1 => ['daily_checks' => 50, 'monthly_checks' => 1000],
    2 => ['daily_checks' => 200, 'monthly_checks' => 5000],
    3 => ['daily_checks' => 500, 'monthly_checks' => 15000]
]);
```

## 📊 Monitoreo

### Logs de Acceso
- **Archivo**: `api/v1.1/core/access_log.txt`
- **Formato**: JSON con timestamp, usuario, gate, resultado
- **Uso**: Auditoría y análisis de uso

### Notificaciones
- **Expiración de suscripción**: 7 días antes
- **Créditos bajos**: Cuando quedan menos de 5
- **Límites alcanzados**: Diarios/mensuales

## 🧪 Pruebas

### Archivo de Prueba
```bash
# Acceder a: http://localhost/test_hybrid_system.php
```

### Casos de Prueba
1. **Usuario con Suscripción Activa** → Acceso sin deducir créditos
2. **Usuario con Suscripción Expirada + Créditos** → Acceso deduciendo créditos
3. **Usuario sin Suscripción ni Créditos** → Acceso denegado
4. **Usuario con Ambos** → Acceso por suscripción (prioridad)

## 🔄 Migración

### Pasos para Implementar
1. **Backup**: Respaldar base de datos
2. **Archivos**: Copiar archivos del sistema híbrido
3. **Gates**: Actualizar cada gate individualmente
4. **Dashboard**: Modificar visualización
5. **Pruebas**: Verificar funcionamiento
6. **Monitoreo**: Revisar logs y métricas

### Rollback
Si hay problemas, simplemente:
1. Revertir cambios en los gates
2. Eliminar archivos híbridos
3. Restaurar funcionalidad original

## 📈 Beneficios

### Para Usuarios
- **Flexibilidad**: Múltiples opciones de pago
- **Transparencia**: Estado claro del acceso
- **Continuidad**: Sin interrupciones en el servicio

### Para Administradores
- **Control**: Gestión centralizada
- **Métricas**: Datos detallados de uso
- **Escalabilidad**: Fácil agregar nuevos gates

## 🚨 Consideraciones

### Seguridad
- **Validación**: Verificación en cada request
- **Logging**: Registro de todos los accesos
- **Límites**: Control de uso por suscripción

### Rendimiento
- **Caching**: Considerar cache para verificaciones frecuentes
- **Índices**: Optimizar consultas de base de datos
- **Logs**: Rotación periódica de archivos de log

## 📞 Soporte

Para problemas o dudas:
1. Revisar logs en `access_log.txt`
2. Ejecutar `test_hybrid_system.php`
3. Verificar configuración en `hybrid_config.php`
4. Contactar al administrador del sistema

---

**🎉 ¡El sistema híbrido está listo para usar!**
