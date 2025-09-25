# 🤖 Configuración del Bot de Telegram para Monitoreo de Seguridad

## 📋 Descripción
Sistema de monitoreo que te notifica en tiempo real cuando los usuarios inician sesión, incluyendo información detallada para detectar accesos compartidos o actividad sospechosa.

## 🔧 Configuración Paso a Paso

### 1. Crear el Bot de Telegram

1. **Abre Telegram** y busca `@BotFather`
2. **Envía el comando**: `/newbot`
3. **Elige un nombre** para tu bot: `DARK CT Security Monitor`
4. **Elige un username**: `darkct_security_bot` (debe terminar en 'bot')
5. **Copia el token** que te proporciona (algo como: `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`)

### 2. Obtener tu Chat ID

1. **Inicia una conversación** con tu bot recién creado
2. **Envía cualquier mensaje** (ej: `/start`)
3. **Abre esta URL** en tu navegador (reemplaza TU_BOT_TOKEN):
   ```
   https://api.telegram.org/botTU_BOT_TOKEN/getUpdates
   ```
4. **Busca en la respuesta** el campo `"chat":{"id":` y copia el número
5. **Ejemplo**: Si ves `"chat":{"id":123456789`, tu Chat ID es `123456789`

### 3. Configurar el Sistema

1. **Edita el archivo**: `telegram_bot_config.php`
2. **Reemplaza**:
   ```php
   define('TELEGRAM_BOT_TOKEN', 'TU_BOT_TOKEN_AQUI');
   define('TELEGRAM_CHAT_ID', 'TU_CHAT_ID_AQUI');
   ```
3. **Con tus datos reales**:
   ```php
   define('TELEGRAM_BOT_TOKEN', '123456789:ABCdefGHIjklMNOpqrsTUVwxyz');
   define('TELEGRAM_CHAT_ID', '123456789');
   ```

### 4. Probar la Configuración

1. **Visita**: `http://tu-dominio.com/telegram_bot_config.php`
2. **Deberías ver**: "Bot funcionando correctamente!"
3. **Recibirás un mensaje** en Telegram confirmando la configuración

## 📊 Información que Recibirás

### Notificación Normal:
```
✅ NUEVO INICIO DE SESIÓN

👤 Usuario: admin
📧 Email: admin@darkct.com
🔑 Estado: Suscripción activa hasta: 15/12/2024
👑 Rol: Administrador

🌐 Información de Conexión:
📍 IP: 192.168.1.100
🌍 Ubicación: Madrid, Madrid, Spain
🌐 Navegador: Chrome
💻 Sistema: Windows
🕒 Fecha: 15/09/2024 14:30:25
🔗 Referer: Direct
🆔 Session ID: abc123def456
```

### Notificación Sospechosa:
```
🚨 NUEVO INICIO DE SESIÓN

👤 Usuario: admin
📧 Email: admin@darkct.com
🔑 Estado: Créditos: 50
👑 Rol: Usuario

🌐 Información de Conexión:
📍 IP: 203.0.113.1
🌍 Ubicación: New York, NY, USA
🌐 Navegador: Firefox
💻 Sistema: Linux
🕒 Fecha: 15/09/2024 14:30:25
🔗 Referer: Direct
🆔 Session ID: xyz789abc123

⚠️ ACTIVIDAD SOSPECHOSA:
Múltiples IPs en 24h: 192.168.1.100, 203.0.113.1
Múltiples ubicaciones: Madrid, Madrid, Spain, New York, NY, USA
Múltiples navegadores: Chrome, Firefox
```

## 🔍 Detección de Actividad Sospechosa

El sistema detecta automáticamente:

- **Múltiples IPs** en 24 horas
- **Ubicaciones diferentes** en poco tiempo
- **Navegadores diferentes** para el mismo usuario
- **Sistemas operativos diferentes**
- **Patrones de acceso inusuales**

## 📈 Panel de Administración

Accede a `admin-login-monitor.php` para ver:

- **Estadísticas** de inicios de sesión
- **Historial completo** de accesos
- **Filtros** por usuario, IP, fecha
- **Alertas** de actividad sospechosa
- **Detalles** de cada sesión

## 🛡️ Características de Seguridad

### Información Recopilada:
- **IP real** del usuario (incluso detrás de proxy)
- **Ubicación geográfica** aproximada
- **Navegador y versión**
- **Sistema operativo**
- **Página de origen** (referer)
- **ID de sesión** único
- **Timestamp** exacto

### Detección de Compartir Cuentas:
- **Múltiples IPs** para el mismo usuario
- **Ubicaciones geográficas** muy distantes
- **Navegadores diferentes** en poco tiempo
- **Patrones de acceso** inusuales

## 🔧 Archivos del Sistema

- `api/v1.1/telegram/login_monitor.php` - Lógica principal
- `telegram_bot_config.php` - Configuración del bot
- `admin-login-monitor.php` - Panel de administración
- `login_monitor_logs` - Tabla de base de datos

## 📱 Comandos del Bot

Puedes enviar estos comandos a tu bot:

- `/start` - Iniciar conversación
- `/help` - Ver ayuda
- `/status` - Estado del sistema
- `/stats` - Estadísticas de accesos

## ⚠️ Consideraciones Importantes

1. **Privacidad**: El sistema recopila información de conexión para seguridad
2. **Almacenamiento**: Los logs se guardan en la base de datos
3. **Retención**: Los logs se mantienen indefinidamente (configurable)
4. **Notificaciones**: Solo recibes notificaciones de inicios de sesión
5. **Acceso**: Solo administradores pueden ver el panel de monitoreo

## 🚀 Beneficios

- **Detección temprana** de accesos compartidos
- **Monitoreo en tiempo real** de la actividad
- **Alertas automáticas** de actividad sospechosa
- **Historial completo** de accesos
- **Información detallada** para investigación
- **Interfaz web** para administración

## 🔒 Seguridad Adicional

El sistema también puede:
- **Bloquear IPs** sospechosas
- **Limitar accesos** por usuario
- **Generar reportes** de seguridad
- **Integrar con** sistemas de alertas externos

¡Tu sistema DARK CT ahora tiene monitoreo de seguridad completo! 🛡️
