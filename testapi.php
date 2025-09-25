<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test API Amazon (Nexo)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
<div class="container py-5">
    <h2 class="mb-4">Probar API Amazon (Nexo)</h2>
    <form id="apiForm" class="bg-secondary p-4 rounded">
        <div class="form-group">
            <label for="cookie">Cookie (raw):</label>
            <textarea class="form-control" id="cookie" rows="3" placeholder="Pega aquí la cookie completa de Amazon" required></textarea>
            <small class="form-text text-muted">La cookie se convertirá automáticamente a Base64</small>
        </div>
        
        <div class="form-group">
            <label for="cookieBase64">Cookie (Base64) - Solo lectura:</label>
            <textarea class="form-control bg-light text-dark" id="cookieBase64" rows="2" placeholder="Aquí aparecerá la cookie en Base64..." readonly></textarea>
            <button type="button" class="btn btn-sm btn-outline-info mt-2" id="copyBase64">Copiar Base64</button>
        </div>
        
        <div class="form-group">
            <label for="cc">Tarjeta (CC):</label>
            <input type="text" class="form-control" id="cc" placeholder="Ej: 5200219129087899|07|2026|354" required>
        </div>
        
        <div class="form-group">
            <label for="pais">País:</label>
            <select class="form-control" id="pais" required>
                <option value="MX">Amazon MX</option>
                <option value="US">Amazon US</option>
                <option value="CA">Amazon CA</option>
                <option value="BR">Amazon BR</option>
                <option value="ES">Amazon ES</option>
                <option value="IT">Amazon IT</option>
                <option value="DE">Amazon DE</option>
                <option value="AE">Amazon AE</option>
                <option value="FR">Amazon FR</option>
                <option value="UK">Amazon UK</option>
                <option value="JP">Amazon JP</option>
                <option value="IN">Amazon IN</option>
                <option value="PL">Amazon PL</option>
                <option value="AU">Amazon AU</option>
                <option value="TR">Amazon TR</option>
                <option value="NL">Amazon NL</option>
                <option value="SA">Amazon SA</option>
                <option value="SG">Amazon SG</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">Probar API</button>
    </form>
    
    <div class="mt-4">
        <h5>Respuesta de la API:</h5>
        <pre id="apiResponse" class="bg-light text-dark p-3 rounded" style="min-height:100px;"></pre>
    </div>
</div>

<script>
// Función para codificar a base64
function base64Encode(str) {
    return btoa(unescape(encodeURIComponent(str)));
}

// Función para decodificar base64 (por si acaso)
function base64Decode(str) {
    return decodeURIComponent(escape(atob(str)));
}

// Convertir cookie a base64 en tiempo real
document.getElementById('cookie').addEventListener('input', function() {
    const cookieRaw = this.value.trim();
    const cookieBase64Field = document.getElementById('cookieBase64');
    
    if (cookieRaw) {
        try {
            const cookieBase64 = base64Encode(cookieRaw);
            cookieBase64Field.value = cookieBase64;
        } catch (error) {
            cookieBase64Field.value = 'Error al convertir a Base64';
        }
    } else {
        cookieBase64Field.value = '';
    }
});

// Copiar Base64 al portapapeles
document.getElementById('copyBase64').addEventListener('click', function() {
    const cookieBase64Field = document.getElementById('cookieBase64');
    if (cookieBase64Field.value) {
        cookieBase64Field.select();
        document.execCommand('copy');
        
        // Feedback visual
        const originalText = this.textContent;
        this.textContent = '¡Copiado!';
        this.classList.add('btn-success');
        this.classList.remove('btn-outline-info');
        
        setTimeout(() => {
            this.textContent = originalText;
            this.classList.remove('btn-success');
            this.classList.add('btn-outline-info');
        }, 2000);
    }
});

// Enviar formulario
document.getElementById('apiForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const cookieRaw = document.getElementById('cookie').value.trim();
    const cc = document.getElementById('cc').value.trim();
    const pais = document.getElementById('pais').value;
    const apiResponse = document.getElementById('apiResponse');
    
    if (!cookieRaw) {
        apiResponse.textContent = 'Error: La cookie es requerida';
        return;
    }
    
    apiResponse.textContent = 'Enviando...';
    
    try {
        // Codificar cookie a base64
        const cookieBase64 = base64Encode(cookieRaw);
        
        // Usar FormData para enviar al proxy PHP
        const formData = new FormData();
        formData.append('ccs', cc);
        formData.append('cookie', cookieBase64);
        formData.append('pais', pais);
        
        const response = await fetch('test_api_proxy.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        apiResponse.textContent = JSON.stringify(result, null, 2);
        
    } catch (err) {
        apiResponse.textContent = 'Error: ' + err.message;
        console.error('Error completo:', err);
    }
});

// Mostrar información de debug
console.log('Formulario cargado correctamente');
</script>
</body>
</html>