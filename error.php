<?php
// Página de error personalizada para IONOS Hosting
$error_code = $_GET['code'] ?? '404';
$error_messages = [
    '400' => 'Solicitud Incorrecta',
    '401' => 'No Autorizado',
    '403' => 'Acceso Prohibido',
    '404' => 'Página No Encontrada',
    '500' => 'Error Interno del Servidor',
    '503' => 'Servicio No Disponible'
];

$error_title = $error_messages[$error_code] ?? 'Error Desconocido';
$error_description = [
    '400' => 'La solicitud no pudo ser procesada debido a un error de sintaxis.',
    '401' => 'Debes iniciar sesión para acceder a esta página.',
    '403' => 'No tienes permisos para acceder a este recurso.',
    '404' => 'La página que buscas no existe o ha sido movida.',
    '500' => 'Ha ocurrido un error interno. Inténtalo más tarde.',
    '503' => 'El servicio está temporalmente no disponible.'
];

$description = $error_description[$error_code] ?? 'Ha ocurrido un error inesperado.';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?php echo $error_code; ?> - ☂ DARK CT ☂</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0f0f0f 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 40px;
            background: rgba(20, 20, 20, 0.95);
            border-radius: 20px;
            border: 1px solid #333;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }
        
        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00ffc4, transparent);
            animation: scanLine 3s infinite;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #00ffc4;
            text-shadow: 0 0 20px rgba(0, 255, 196, 0.5);
            margin-bottom: 20px;
            line-height: 1;
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        
        .error-description {
            font-size: 1.1rem;
            color: #cccccc;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(45deg, #00ffc4, #0099ff);
            color: #000;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgba(0, 255, 196, 0.2);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 255, 196, 0.3);
            color: #000;
        }
        
        .btn-secondary {
            background: linear-gradient(45deg, #333, #555);
            color: #fff;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        }
        
        .btn-secondary:hover {
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.4);
            color: #fff;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            color: #00ffc4;
            margin-bottom: 30px;
            text-shadow: 0 0 15px rgba(0, 255, 196, 0.5);
        }
        
        @keyframes scanLine {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        @media (max-width: 768px) {
            .error-container {
                margin: 20px;
                padding: 30px 20px;
            }
            
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .error-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="logo">☂ DARK CT ☂</div>
        <div class="error-code"><?php echo $error_code; ?></div>
        <div class="error-title"><?php echo $error_title; ?></div>
        <div class="error-description"><?php echo $description; ?></div>
        
        <div class="error-actions">
            <a href="/" class="btn">🏠 Ir al Inicio</a>
            <a href="javascript:history.back()" class="btn btn-secondary">⬅️ Volver Atrás</a>
        </div>
    </div>
</body>
</html>
