# 📁 UBICACIÓN COMPLETA DE IMÁGENES - DARK CT

## 🖼️ IMÁGENES PRINCIPALES

### **Favicon y Iconos**
- **Favicon principal**: `/favicon.ico` (raíz del proyecto)
- **Icono Amazon**: `/static/v4/img/294695_amazon_icon.ico`

### **Imágenes de Usuario y Perfil**
- **Logo Okura**: `/static/v4/img/okurachk.jpg`
- **Foto de perfil**: `/static/v4/img/photo_2025-07-09_13-53-04.jpg`
- **Imagen XD**: `/static/v4/img/xd.jpg`
- **Imagen PNG**: `/static/v4/img/image.png`

### **Imágenes de Interfaz**
- **Loading GIF**: `/static/v4/img/loading2.gif`
- **Nuevo GIF**: `/static/v4/img/new.gif`

## 🎨 FONDOS DE PANTALLA

### **Carpeta: `/static/v4/img/bg/`**
- `1.jpg` - Fondo 1
- `2.jpg` - Fondo 2
- `3.jpg` - Fondo 3
- `4.jpg` - Fondo 4
- `5.jpg` - Fondo 5
- `6.jpg` - Fondo 6
- `7.jpg` - Fondo 7
- `8.jpg` - Fondo 8
- `9.jpg` - Fondo 9
- `10.jpg` - Fondo 10

## 📅 IMÁGENES DE CALENDARIO

### **Carpeta: `/static/v4/img/calendar/`**
- `1.jpg` - Calendario 1
- `2.jpg` - Calendario 2
- `3.jpg` - Calendario 3
- `4.jpg` - Calendario 4
- `5.jpg` - Calendario 5
- `6.jpg` - Calendario 6
- `7.jpg` - Calendario 7
- `8.jpg` - Calendario 8
- `9.jpg` - Calendario 9
- `10.jpg` - Calendario 10
- `11.jpg` - Calendario 11
- `12.jpg` - Calendario 12

## 🎨 ICONOS Y FORMULARIOS

### **Carpeta: `/static/v4/img/forms/`**
- `checkbox-checked.svg` - Checkbox marcado
- `select-caret.svg` - Flecha de select

## 🔤 FUENTES E ICONOS

### **Fuentes Nunito: `/static/v4/fonts/nunito/`**
- `nunito-bold.woff`
- `nunito-bold.woff2`
- `nunito-regular.woff2`
- `nunito-semibold.woff2`

### **Iconos Themify: `/static/v4/plugins/icon/themify-icons/fonts/`**
- `themify9f24.eot`
- `themify9f24.ttf`
- `themify9f24.woff`
- `themify9f24.woff2`

### **Iconos Zwicon: `/static/v4/vendors/zwicon/fonts/`**
- `zwicondf5adf5a.eot`
- `zwicondf5adf5a.svg`
- `zwicondf5adf5a.ttf`
- `zwicondf5adf5a.woff`

## 🎵 ARCHIVOS DE AUDIO

### **Carpeta: `/static/v4/sounds/`**
- `iniciar.wav` - Sonido de inicio
- `live.mp3` - Sonido de live

## 📋 RESUMEN DE UBICACIONES

### **Rutas Absolutas (desde la raíz del proyecto)**
```
/favicon.ico
/static/v4/img/294695_amazon_icon.ico
/static/v4/img/okurachk.jpg
/static/v4/img/photo_2025-07-09_13-53-04.jpg
/static/v4/img/xd.jpg
/static/v4/img/image.png
/static/v4/img/loading2.gif
/static/v4/img/new.gif
/static/v4/img/bg/1.jpg - 10.jpg
/static/v4/img/calendar/1.jpg - 12.jpg
/static/v4/img/forms/checkbox-checked.svg
/static/v4/img/forms/select-caret.svg
/static/v4/sounds/iniciar.wav
/static/v4/sounds/live.mp3
```

### **Rutas Relativas (desde cualquier archivo PHP)**
```php
// Ejemplos de uso en PHP
$favicon = "/favicon.ico";
$logo = "/static/v4/img/okurachk.jpg";
$loading = "/static/v4/img/loading2.gif";
$bg_image = "/static/v4/img/bg/1.jpg";
$calendar_img = "/static/v4/img/calendar/1.jpg";
```

### **Rutas en CSS**
```css
/* Ejemplos de uso en CSS */
background-image: url('/static/v4/img/bg/1.jpg');
background-image: url('../img/loading2.gif');
```

## 🔍 BÚSQUEDA DE IMÁGENES EN EL CÓDIGO

### **Archivos que referencian imágenes:**
- `sign-in.php` - Logo y favicon
- `sign-up.php` - Favicon
- `static/v4/plugins/form/header.php` - Logo de usuario
- `gates/amazon.php` - Loading GIF
- `gates/chase_gate.php` - Loading GIF
- `gates/paypal_gate.php` - Loading GIF
- `gates/stripe_gate.php` - Loading GIF
- `gates/stripezoura.php` - Loading GIF
- `gates/stripe.php` - Loading GIF
- `gates/paypal.php` - Loading GIF
- `gates/chaze.php` - Loading GIF
- `gates/zchaze.php` - Loading GIF
- `stripe/stripe.php` - Logo de usuario

## 📊 ESTADÍSTICAS DE IMÁGENES

- **Total de imágenes**: 32 archivos
- **Formatos soportados**: JPG, PNG, GIF, SVG, ICO, WAV, MP3
- **Carpetas principales**: 6 directorios
- **Imágenes de fondo**: 10 archivos
- **Imágenes de calendario**: 12 archivos
- **Iconos y fuentes**: 8 archivos

## ⚠️ NOTAS IMPORTANTES

1. **Todas las rutas son relativas a la raíz del proyecto**
2. **Las imágenes están organizadas por categorías**
3. **Los archivos de audio están en la carpeta `/sounds/`**
4. **Las fuentes están en `/fonts/` y `/vendors/`**
5. **Los iconos SVG están en `/forms/`**
6. **El favicon debe estar en la raíz del proyecto**

## 🚀 CONFIGURACIÓN RECOMENDADA

Para un despliegue en producción, asegúrate de:
1. Copiar todas las carpetas de imágenes
2. Verificar permisos de lectura
3. Configurar cache para imágenes estáticas
4. Optimizar imágenes para web
5. Usar CDN si es necesario

