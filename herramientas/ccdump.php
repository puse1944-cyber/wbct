<?php $site_page = "Search Extra"; $path = $_SERVER["DOCUMENT_ROOT"]; include($path."/static/v4/plugins/form/header.php"); ?>

<style>
/* Estilos personalizados para Search Extra */
.search-extra-container {
    background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
    min-height: 100vh;
    padding: 20px;
}

.search-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px 0;
    background: rgba(0, 0, 0, 0.8);
    border-radius: 20px;
    border: 2px solid #333;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.search-title {
    font-size: 3rem;
    font-weight: 900;
    background: linear-gradient(45deg, #00ffc4, #0099ff, #ff00ff);
    background-size: 300% 300%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: gradientShift 3s ease-in-out infinite;
    margin-bottom: 10px;
    text-shadow: 0 0 30px rgba(0, 255, 196, 0.5);
}

.search-subtitle {
    color: #cccccc;
    font-size: 1.2rem;
    margin-bottom: 20px;
}

.search-input-container {
    position: relative;
    max-width: 500px;
    margin: 0 auto;
}

.search-input {
    width: 100%;
    padding: 20px 60px 20px 20px;
    font-size: 1.5rem;
    background: rgba(0, 0, 0, 0.9);
    border: 2px solid #333;
    border-radius: 50px;
    color: #ffffff;
    text-align: center;
    letter-spacing: 2px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.search-input:focus {
    outline: none;
    border-color: #00ffc4;
    box-shadow: 0 0 20px rgba(0, 255, 196, 0.3);
    transform: translateY(-2px);
}

.search-icon {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #00ffc4;
    font-size: 1.5rem;
}

.results-container {
    margin-top: 40px;
    min-height: 400px;
}

.result-card {
    background: rgba(20, 20, 20, 0.9);
    border: 1px solid #333;
    border-radius: 15px;
    padding: 25px;
    margin: 15px 0;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.result-card:hover {
    border-color: #00ffc4;
    box-shadow: 0 10px 30px rgba(0, 255, 196, 0.2);
    transform: translateY(-5px);
}

.result-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #00ffc4, transparent);
    animation: scanLine 2s infinite;
}

.card-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.card-badge {
    background: linear-gradient(45deg, #00ffc4, #0099ff);
    color: #000;
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: bold;
    font-size: 0.9rem;
    margin-right: 15px;
    box-shadow: 0 3px 10px rgba(0, 255, 196, 0.3);
}

.card-type {
    color: #ffffff;
    font-weight: bold;
    font-size: 1.1rem;
}

.card-number {
    font-family: 'Courier New', monospace;
    font-size: 1.3rem;
    color: #00ffc4;
    margin: 10px 0;
    letter-spacing: 1px;
}

.card-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 15px;
}

.info-item {
    background: rgba(0, 0, 0, 0.5);
    padding: 10px;
    border-radius: 8px;
    text-align: center;
}

.info-label {
    color: #999;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.info-value {
    color: #ffffff;
    font-weight: bold;
    margin-top: 5px;
}

.loading-container {
    text-align: center;
    padding: 60px 20px;
}

.loading-spinner {
    width: 60px;
    height: 60px;
    border: 4px solid #333;
    border-top: 4px solid #00ffc4;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

.loading-text {
    color: #00ffc4;
    font-size: 1.2rem;
    font-weight: bold;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-text {
    font-size: 1.2rem;
    margin-bottom: 10px;
}

.empty-subtext {
    font-size: 1rem;
    color: #999;
}

.stats-container {
    background: rgba(0, 0, 0, 0.8);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
    border: 1px solid #333;
    text-align: center;
}

.stats-text {
    color: #00ffc4;
    font-size: 1.1rem;
    font-weight: bold;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes scanLine {
    0% { left: -100%; }
    100% { left: 100%; }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .search-title {
        font-size: 2rem;
    }
    
    .search-input {
        font-size: 1.2rem;
        padding: 15px 50px 15px 15px;
    }
    
    .card-info {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="content">
    <div class="search-extra-container">
        <!-- Header Section -->
        <div class="search-header">
            <h1 class="search-title">🔍 SEARCH EXTRA</h1>
            <p class="search-subtitle">Búsqueda Avanzada de Tarjetas de Crédito</p>
            <div class="search-input-container">
                <input type="text" 
                       class="search-input" 
                       id="buscador" 
                       name="buscador" 
                       placeholder="Ingresa BIN de 6 dígitos" 
                       maxlength="6"
                       onkeyup="buscar_ahora($('#buscador').val());">
                <i class="fa fa-search search-icon"></i>
            </div>
        </div>

        <!-- Results Container -->
        <div class="results-container" id="results-container">
            <div class="empty-state" id="empty-state">
                <div class="empty-icon">
                    <i class="fa fa-search"></i>
                </div>
                <div class="empty-text">Búsqueda de Tarjetas</div>
                <div class="empty-subtext">Ingresa un BIN de 6 dígitos para comenzar la búsqueda</div>
            </div>
        </div>
    </div>
</section>

<script>
// Función para mostrar loader
function showLoading() {
    $('#results-container').html(`
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <div class="loading-text">Buscando tarjetas...</div>
        </div>
    `);
}

// Función para mostrar estado vacío
function showEmptyState(message = "Ingresa un BIN de 6 dígitos para comenzar la búsqueda", icon = "fa-search") {
    $('#results-container').html(`
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fa ${icon}"></i>
            </div>
            <div class="empty-text">${message}</div>
        </div>
    `);
}

// Función para mostrar resultados
function showResults(data) {
    $('#results-container').html(data);
}

// Función para mostrar estadísticas
function showStats(count, bin) {
    const statsHtml = `
        <div class="stats-container">
            <div class="stats-text">
                🎯 Encontradas ${count} tarjetas para el BIN: ${bin}
            </div>
        </div>
    `;
    return statsHtml;
}

// Función para buscar CCs
function buscar_ahora(buscar) {
    var parametros = {"buscar": buscar};
    
    if (buscar.length == 0) {
        showEmptyState("Ingresa un BIN de 6 dígitos para comenzar la búsqueda", "fa-search");
        return;
    }
    else if (buscar.length < 6) {
        showEmptyState(`BIN incompleto. Ingresa 6 dígitos (${buscar.length}/6)`, "fa-info-circle");
        return;
    }
    else if (buscar.length == 6) {
        showLoading();
        
        $.ajax({
            data: parametros,
            type: 'POST',
            url: '/api/v1.1/core/cc.php',
            success: function(data) {
                if (data.trim() === '') {
                    showEmptyState(`No se encontraron resultados para el BIN: ${buscar}`, "fa-exclamation-triangle");
                } else {
                    showResults(data);
                }
            },
            error: function(xhr, status, error) {
                showEmptyState(`Error al buscar: ${error}`, "fa-exclamation-circle");
            }
        });
    }
}

// Efectos visuales adicionales
function addVisualEffects() {
    // Efecto de typing en el input
    $('#buscador').on('input', function() {
        const value = $(this).val();
        if (value.length > 0 && value.length < 6) {
            $(this).css('border-color', '#f39c12');
        } else if (value.length == 6) {
            $(this).css('border-color', '#00ffc4');
        } else {
            $(this).css('border-color', '#333');
        }
    });
    
    // Efecto de focus
    $('#buscador').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });
}

// Inicializar la página
$(document).ready(function() {
    showEmptyState("Ingresa un BIN de 6 dígitos para comenzar la búsqueda", "fa-search");
    addVisualEffects();
    
    // Auto-focus en el input
    setTimeout(() => {
        $('#buscador').focus();
    }, 500);
});
</script>

</body>
</html>