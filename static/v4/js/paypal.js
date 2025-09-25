$(document).ready(function() {
    // Inicialización de variables
    let listaCartoes = [];
    let running = false;
    let testeadas = 0;
    let indice = 0;
    let livesNum = 0;
    let deadsNum = 0;
    let errorNum = 0;
    let paused = false;

    // Función para inicializar overlayScrollbars
    function initOverlayScrollbars() {
        if (typeof OverlayScrollbars !== 'undefined') {
            OverlayScrollbars(document.querySelectorAll('.table-responsive'), {
                scrollbars: {
                    theme: 'os-theme-dark'
                }
            });
        }
    }

    // Inicializar overlayScrollbars después de que se cargue la página
    setTimeout(initOverlayScrollbars, 1000);

    // Asignar la función genCC al botón
    $('.btn-theme').on('click', function() {
        if ($(this).text().includes('Generate Cards')) {
            genCC();
        }
    });

    // Manejar el inicio del check
    $('#chk-start').on('click', async function() {
        const tarjetas = $('#result').val().trim().split("\n").filter(t => t.trim() !== "");

        if (tarjetas.length === 0) {
            Swal.fire("⚠️ Lista vacía", "Ingresa tarjetas para procesar.", "warning");
            return;
        }

        $('#estatus').removeClass().addClass('badge badge-info').text("Procesando tarjetas...");
        running = true;
        paused = false;
        listaCartoes = tarjetas;
        testeadas = 0;
        indice = 0;
        livesNum = 0;
        deadsNum = 0;
        errorNum = 0;

        // Actualizar contadores
        $('#total').text(listaCartoes.length);
        $('#checked').text('0');
        $('#livestat').text('0');
        $('#diestat').text('0');
        $('#errorstat').text('0');

        // Limpiar resultados anteriores
        $('#lives').empty();
        $('#dies').empty();
        $('#errors').empty();

        // Mostrar loader
        $('#loader').show();

        // Iniciar procesamiento
        processCards();
    });

    // Manejar pausa
    $('#chk-pause').on('click', function() {
        if (running) {
            paused = !paused;
            if (paused) {
                $(this).html('<i class="fas fa-play"></i> Reanudar');
                $('#estatus').text("Pausado");
            } else {
                $(this).html('<i class="fas fa-pause"></i> Pausar');
                $('#estatus').text("Procesando...");
                processCards();
            }
        }
    });

    // Manejar detener
    $('#chk-stop').on('click', function() {
        running = false;
        paused = false;
        $('#estatus').text("Detenido");
        $('#loader').hide();
        $('#chk-pause').html('<i class="fas fa-pause"></i> Pausar');
    });

    // Función para procesar tarjetas
    async function processCards() {
        while (running && indice < listaCartoes.length && !paused) {
            const tarjeta = listaCartoes[indice];
            
            try {
                const resultado = await checkCard(tarjeta);
                handleResult(tarjeta, resultado);
            } catch (error) {
                handleError(tarjeta, error.message);
            }
            
            indice++;
            testeadas++;
            $('#checked').text(testeadas);
            
            // Actualizar barra de progreso
            const porcentaje = Math.round((testeadas / listaCartoes.length) * 100);
            $('#porcentaje').text(`Progreso: ${porcentaje}%`);
            
            // Pequeña pausa entre requests
            await new Promise(resolve => setTimeout(resolve, 2000));
        }
        
        if (indice >= listaCartoes.length) {
            finishProcess();
        }
    }

    // Función para verificar una tarjeta
    async function checkCard(tarjeta) {
        const formData = new FormData();
        formData.append('lista', tarjeta);
        formData.append('csrf_token', $('#csrf_token').val());

        const response = await fetch('/gates/paypal_api.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Error del servidor: ${response.status}`);
        }

        return await response.json();
    }

    // Función para manejar resultados
    function handleResult(tarjeta, resultado) {
        if (resultado.status === 'success') {
            livesNum++;
            $('#livestat').text(livesNum);
            $('#lives').prepend(`<div class="alert alert-success">✅ ${tarjeta} - ${resultado.message}</div>`);
            document.getElementById('detectaLive').play();
        } else {
            deadsNum++;
            $('#diestat').text(deadsNum);
            $('#dies').prepend(`<div class="alert alert-danger">❌ ${tarjeta} - ${resultado.message}</div>`);
        }
    }

    // Función para manejar errores
    function handleError(tarjeta, error) {
        errorNum++;
        $('#errorstat').text(errorNum);
        $('#errors').prepend(`<div class="alert alert-warning">⚠️ ${tarjeta} - ${error}</div>`);
    }

    // Función para finalizar proceso
    function finishProcess() {
        running = false;
        $('#estatus').text("Completado");
        $('#loader').hide();
        $('#chk-pause').html('<i class="fas fa-pause"></i> Pausar');
        
        Swal.fire({
            title: "¡Proceso completado!",
            text: `Procesadas: ${testeadas} | Lives: ${livesNum} | Dies: ${deadsNum} | Errores: ${errorNum}`,
            icon: "success"
        });
    }

    // Función para generar tarjetas (si es necesaria)
    function genCC() {
        // Implementar generación de tarjetas si es necesario
        console.log("Generar tarjetas");
    }
});

