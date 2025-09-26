<?php
/**
 * Script para limpiar archivos JavaScript con caracteres ilegales
 */

echo "🔧 Limpiando archivos JavaScript con caracteres ilegales...\n\n";

$files_to_fix = [
    'static/v4/vendors/bootstrap/js/bootstrap.min.js',
    'static/v4/vendors/overlay-scrollbars/jquery.overlayScrollbars.min.js',
    'static/v4/plugins/form/summernote/summernote.min.js'
];

foreach ($files_to_fix as $file) {
    if (file_exists($file)) {
        echo "📁 Procesando: $file\n";
        
        // Leer el archivo
        $content = file_get_contents($file);
        
        // Limpiar caracteres ilegales
        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);
        $content = str_replace(["\xEF\xBF\xBD", ""], '', $content);
        
        // Escribir el archivo limpio
        if (file_put_contents($file, $content)) {
            echo "✅ Limpiado exitosamente\n";
        } else {
            echo "❌ Error al escribir el archivo\n";
        }
    } else {
        echo "⚠️  Archivo no encontrado: $file\n";
    }
    echo "\n";
}

echo "🎉 Proceso completado!\n";
echo "💡 Si los errores persisten, considera reemplazar estos archivos con versiones originales.\n";
?>
