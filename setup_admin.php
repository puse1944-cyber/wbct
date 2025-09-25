<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario Administrador - Dark CT</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0f0f0f 100%);
            color: #ffffff;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(20, 20, 20, 0.95);
            border: 1px solid #333;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        h1 {
            color: #00ffc4;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(0, 255, 196, 0.5);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #cccccc;
            font-weight: 600;
        }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.8);
            color: #ffffff;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="number"]:focus {
            outline: none;
            border-color: #00ffc4;
            box-shadow: 0 0 10px rgba(0, 255, 196, 0.3);
        }
        .btn {
            background: linear-gradient(45deg, #00ffc4, #0099ff);
            color: #000;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 255, 196, 0.4);
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 600;
        }
        .success {
            background: rgba(0, 255, 196, 0.1);
            border: 1px solid #00ffc4;
            color: #00ffc4;
        }
        .error {
            background: rgba(255, 68, 68, 0.1);
            border: 1px solid #ff4444;
            color: #ff4444;
        }
        .warning {
            background: rgba(255, 215, 0, 0.1);
            border: 1px solid #ffd700;
            color: #ffd700;
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Crear Usuario Administrador</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Configuración de la base de datos
            $host = 'localhost';
            $dbname = 'darkct';
            $username = 'root';
            $password = '';
            
            // Obtener datos del formulario
            $admin_username = $_POST['username'] ?? '';
            $admin_email = $_POST['email'] ?? '';
            $admin_password = $_POST['password'] ?? '';
            $admin_credits = (int)($_POST['credits'] ?? 1000);
            $subscription_days = (int)($_POST['subscription_days'] ?? 365);
            
            try {
                // Conectar a la base de datos
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Verificar si el usuario ya existe
                $check_query = $pdo->prepare("SELECT id FROM breathe_users WHERE username = ? OR email = ?");
                $check_query->execute([$admin_username, $admin_email]);
                
                if ($check_query->fetch()) {
                    echo '<div class="result error">❌ Error: El usuario o email ya existe.</div>';
                } else {
                    // Hashear la contraseña
                    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
                    
                    // Calcular fecha de expiración
                    $expiration_date = date('Y-m-d', strtotime("+$subscription_days days"));
                    
                    // Insertar usuario administrador
                    $insert_query = $pdo->prepare("
                        INSERT INTO breathe_users (
                            username, 
                            email, 
                            breathe_password, 
                            creditos, 
                            suscripcion, 
                            fech_reg, 
                            active,
                            created_at
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                    ");
                    
                    $result = $insert_query->execute([
                        $admin_username,
                        $admin_email,
                        $hashed_password,
                        $admin_credits,
                        3, // Suscripción admin
                        $expiration_date,
                        1  // Activo
                    ]);
                    
                    if ($result) {
                        $user_id = $pdo->lastInsertId();
                        
                        echo '<div class="result success">';
                        echo '✅ Usuario administrador creado exitosamente!<br><br>';
                        echo '<strong>📋 Detalles del usuario:</strong><br>';
                        echo "ID: $user_id<br>";
                        echo "Username: $admin_username<br>";
                        echo "Email: $admin_email<br>";
                        echo "Password: $admin_password<br>";
                        echo "Créditos: $admin_credits<br>";
                        echo "Suscripción: Administrador (3)<br>";
                        echo "Expira: $expiration_date<br>";
                        echo "Estado: Activo<br>";
                        echo '</div>';
                        
                        echo '<div class="warning">';
                        echo '⚠️ <strong>IMPORTANTE:</strong><br>';
                        echo '• Cambia la contraseña después del primer login<br>';
                        echo '• ELIMINA este archivo después de usarlo por seguridad<br>';
                        echo '• Accede a: <a href="/user/sign-in" style="color: #00ffc4;">/user/sign-in</a>';
                        echo '</div>';
                    } else {
                        echo '<div class="result error">❌ Error al crear el usuario administrador.</div>';
                    }
                }
                
            } catch (PDOException $e) {
                echo '<div class="result error">❌ Error de base de datos: ' . htmlspecialchars($e->getMessage()) . '</div>';
            } catch (Exception $e) {
                echo '<div class="result error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
        ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">👤 Username:</label>
                <input type="text" id="username" name="username" value="admin" required>
            </div>
            
            <div class="form-group">
                <label for="email">📧 Email:</label>
                <input type="email" id="email" name="email" value="admin@darkct.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Password:</label>
                <input type="password" id="password" name="password" value="admin123" required>
            </div>
            
            <div class="form-group">
                <label for="credits">💰 Créditos iniciales:</label>
                <input type="number" id="credits" name="credits" value="1000" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="subscription_days">📅 Días de suscripción:</label>
                <input type="number" id="subscription_days" name="subscription_days" value="365" min="1" required>
            </div>
            
            <button type="submit" class="btn">🚀 Crear Usuario Administrador</button>
        </form>
        
        <div class="warning">
            ⚠️ <strong>Nota de Seguridad:</strong><br>
            • Cambia los valores por defecto antes de crear el usuario<br>
            • Usa una contraseña segura<br>
            • Elimina este archivo después de usarlo
        </div>
        
        <?php } ?>
    </div>
</body>
</html>
