$(document).ready(function() {
    var Cards;
    var numCards;
    var Proxys;
    var numProxys;
    var running;
    var livesNum = 0;
    var deadsNum = 0;
    var errorNum = 0;
    var token;
    var route;
    var tipoProxy;
    var testeadas = 0;
    var indice = 0;

    $('#bin').focusout(function() {
        var _txtCreditCardBIN = $('#bin');
        var bin = _txtCreditCardBIN.val();
        if (bin.startsWith('4') || bin.startsWith('5') || bin.startsWith('6')) {
            if (bin.length < 16) {
                var countRest = 16 - bin.length;
                for (var i = 0; i < countRest; i++) {
                    bin = bin.toLowerCase() + 'x'
                }
                _txtCreditCardBIN.val(bin)
            } else {
                bin = bin.toLowerCase().substring(0, 16);
                _txtCreditCardBIN.val(bin);
            }
        } else if (bin.startsWith('3')) {
            if (bin.length < 15) {
                var countRest = 15 - bin.length;
                for (var i = 0; i < countRest; i++) {
                    bin = bin.toLowerCase() + 'x'
                }
                _txtCreditCardBIN.val(bin)
            } else {
                bin = bin.toLowerCase().substring(0, 15);
                _txtCreditCardBIN.val(bin);
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Ingresa un bin valido'
            })
            return
        }
    });

    $("#iniciar").click(function() {
        formatcc();
        var lista = $('#result').val();
        if (lista.length == 0) {
            swal.fire({
                title: "CC List is Null",
                type: 'warning',
                text: "Tu lista de CCs esta vacia, por favor ingrese CCs!",
                background: "#000",
                backdrop: "rgba(0,0,0,0.2)",
                buttonsStyling: !1,
                padding: "3rem 3rem 2rem",
                customClass: {
                    confirmButton: "btn btn-link",
                    title: "ca-title",
                    container: "ca"
                }
            })
        } else {
            testeadas = 0;
            indice = 0;
            document.getElementById('iniciaSonido').play();
            Cards = $('#result').val().split('\n');
            numCards = Cards.length;
            $('#total').text(numCards);
            document.getElementById('loader').style.display = 'block';
            document.getElementById('datalog').style.display = 'block';
            document.getElementById('barra_porcentaje').style.display = 'block';
            $('#porcentaje').html('Gateway Started!...');
            $("#state").html("<b>Estamos procesando tus CCs, espere por favor...</b>");
            running = true;
            recursivePost(0);
            document.getElementById("iniciar").disabled = true;
            document.getElementById("detener").disabled = false;
        }
    });

    $("#detener").click(function() {
        if (running == true) {
            running = false;
            document.getElementById('loader').style.display = 'none';
            document.getElementById('datalog').style.display = 'block';
            document.getElementById("porcentaje").style.width = "100%";
            $('#porcentaje').html('Proceso Detenido...');
            document.getElementById("iniciar").disabled = false;
            document.getElementById("detener").disabled = true;
        } else {
            document.getElementById("iniciar").disabled = false;
            document.getElementById("detener").disabled = true;
        }
    });

    function recursivePost(indice) {
        if (!running || indice >= Cards.length) {
            finalizar();
            return;
        }

        var CardElements = Cards[indice];
        var start_time = new Date().getTime();
        var parametros = {
            ccs: CardElements
        }

        $.ajax({
            url: 'stripe_api.php',
            type: 'POST',
            data: parametros,
            dataType: "json",
            success: function(data, status, request) {
                try {
                    if (data.status) {
                        $("#state").html(data['status']);
                        if (data.status.includes("Approved") !== false) {
                            testeadas++;
                            indice++;
                            $('#checked').text(testeadas);
                            deleteLineAux = $('#result').val().split('\n');
                            deleteLineAux.splice(0, 1);
                            document.getElementById("result").value = deleteLineAux.join('\n');
                            document.getElementById('detectaLive').play();
                            livesNum++;
                            $("#viva").append("<tr><td>" + data['card'] + "</td></tr>");
                            $('#livestat').text(livesNum);
                            $('#live_num').html(livesNum);
                            
                            // Si está marcada la opción de detener al encontrar una live
                            if (document.getElementById('customCheckDisabled1').checked) {
                                running = false;
                                finalizar();
                                document.getElementById("iniciar").disabled = false;
                                document.getElementById("detener").disabled = true;
                                return;
                            }
                        } else if (data.status.includes("Declined") !== false) {
                            testeadas++;
                            indice++;
                            $('#checked').text(testeadas);
                            deleteLineAux = $('#result').val().split('\n');
                            deleteLineAux.splice(0, 1);
                            document.getElementById("result").value = deleteLineAux.join('\n');
                            deadsNum++;
                            $("#muerta").append("<tr><td>" + data['card'] + "</td></tr>");
                            $('#diestat').text(deadsNum);
                            $('#die_num').html(deadsNum);
                        } else {
                            $('#checked').text(testeadas);
                            $("#error").append("<tr><td>" + data['card'] + "</td></tr>");
                        }

                        var width = Number((((indice / Cards.length) * 100)).toFixed(2));
                        if (indice < numCards && running == true && Cards[indice] !== "") {
                            document.getElementById("porcentaje").style.width = width + '%';
                            $('#porcentaje').html(width + '% Completado');
                            // Esperar 2 segundos antes de la siguiente solicitud
                            setTimeout(function() {
                                recursivePost(indice);
                            }, 2000);
                        } else {
                            finalizar();
                        }
                    }
                } catch (error) {
                    console.error('Error procesando respuesta:', error);
                    $("#error").append("<tr><td>Error procesando respuesta</td></tr>");
                    setTimeout(function() {
                        recursivePost(indice);
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud:', error);
                $("#error").append("<tr><td>Error en la solicitud</td></tr>");
                setTimeout(function() {
                    recursivePost(indice);
                }, 2000);
            }
        });
    }


    function finalizar() {
        running = false;
        document.getElementById("porcentaje").style.width = "100%";
        $('#porcentaje').html('Proceso Finalizado');
        document.getElementById('loader').style.display = 'none';
        $("#state").html("<b>Finalizado!</b>");
        document.getElementById("iniciar").disabled = false;
        document.getElementById("detener").disabled = true;
    }

    const cardRegex = /4[0-9]{15}[^\d][0-9]{1,4}[^\d][0-9]{1,4}[^\d][0-9]{3}|5[0-9]{15}[^\d][0-9]{1,4}[^\d][0-9]{1,4}[^\d][0-9]{3}|6[0-9]{15}[^\d][0-9]{1,4}[^\d][0-9]{1,4}[^\d][0-9]{3}|3[0-9]{14}[^\d][0-9]{1,4}[^\d][0-9]{1,4}[^\d][0-9]{4}/g;

    String.prototype.isEmpty = function() {
        return (this.trim() === '');
    }
    Array.prototype.unique = function() {
        return this.filter((x, i, a) => a.indexOf(x) == i);
    }
    Array.prototype.swap = function(indexA, indexB) {
        return this.splice(indexA, 1, this[indexB])[0];
    }

    const parser = (text) => {
        let pos = 0;
        let currChar = text[pos];
        let tokens = [];

        while (currChar !== undefined) {
            if (/^\d$/.test(currChar)) {
                var acc = currChar;
                while (/^\d$/.test(text[pos])) {
                    acc += text[pos++];
                }

                if (['4', '5', '6'].indexOf(acc[0]) && acc.length === 16) {
                    tokens.push(['NORMALPAN', acc]);
                } else if (acc[0] == 3 && acc.length === 15) {
                    tokens.push(['AMEXPAN', acc]);
                } else if (/^(0?[1-9]|1[0-2])$/.test(acc)) {
                    tokens.push(['MONTH', acc]);
                } else if (/^((20)?[2-3][0-9])$/.test(acc)) {
                    tokens.push(['YEAR', acc]);
                } else if (acc.length === 3) {
                    tokens.push(['NORMALCVV', acc]);
                } else if (acc.length === 4) {
                    tokens.push(['AMEXCVV', acc]);
                }
            }
            currChar = text[pos++];
        }

        return tokens;
    }

    function formatcc() {
        const dataccs = document.getElementById("result").value;
        if (dataccs.isEmpty()) return;
        var filtradas = dataccs.match(cardRegex);
        if (filtradas === null) {
            let tokens = parser(dataccs);
            let tokPos = 0;
            let currTok = tokens[tokPos];
            filtradas = [];

            while (currTok !== undefined) {
                let [type, value] = currTok;

                if (type === 'NORMALPAN') {
                    let final = {};
                    final.pan = value;
                    currTok = tokens[tokPos++];
                    [type, value] = currTok;

                    if (type === 'MONTH') {
                        final.month = value;
                        currTok = tokens[tokPos++];
                        [type, value] = currTok;

                        if (type === 'YEAR') {
                            final.year = value;
                            currTok = tokens[tokPos++];
                            [type, value] = currTok;

                            if (type === 'NORMALCVV') {
                                final.cvv = value;
                                currTok = tokens[tokPos++];
                            }
                        }
                    } else if (type === 'NORMALCVV') {
                        final.cvv = value;
                        currTok = tokens[tokPos++];
                        [type, value] = currTok;

                        if (type === 'MONTH') {
                            final.month = value;
                            currTok = tokens[tokPos++];
                            [type, value] = currTok;

                            if (type === 'YEAR') {
                                final.year = value;
                                currTok = tokens[tokPos++];
                            }
                        }
                    }
                    let armaderia = final.pan + '|' + final.month + '|' + final.year + '|' + final.cvv;
                    filtradas.push(armaderia);
                }
                currTok = tokens[tokPos++];
            }
        }
        var fecha = new Date();
        var year = fecha.getFullYear();
        var mes = fecha.getMonth();
        let reunidas = filtradas.map(elemento => {
            let tarjeta = elemento.split(/[^\d]/g);
            if (tarjeta[1] > tarjeta[2]) tarjeta[1] = tarjeta.swap(1, 2);
            if (tarjeta[2].length == 2) tarjeta[2] = "20" + tarjeta[2];
            if (tarjeta[2] > year || (tarjeta[2] == year && tarjeta[1] > mes)) return tarjeta.join("|");
            else return null;
        }).filter((v) => v !== null);
        reunidas = reunidas.unique();
        console.log(reunidas);
        document.getElementById("result").value = reunidas.join("\n");
    }
});

function genCC() {
    var _txtCreditCardBIN = $('#bin');
    var cc_post = _txtCreditCardBIN.val();

    var _txtCreditCardCAN = $('#cantidad');
    var cantidad_post = _txtCreditCardCAN.val();

    var _txtCreditCardMES = $('#mes');
    var fech_post_mes = _txtCreditCardMES.val();

    var _txtCreditCardANO = $('#anio');
    var fech_post_year = _txtCreditCardANO.val();


    var _txtCreditCardCVV = $('#cvc');
    var cvc = _txtCreditCardCVV.val();

    if (cc_post.startsWith('4') || cc_post.startsWith('5') || cc_post.startsWith('6') || cc_post.startsWith('3')) {
        ccgen(cc_post, fech_post_mes, fech_post_year, cvc, cantidad_post);
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Ingresa un bin valido'
        })
        return
    }
}


$(document).ready(function() {
    $('.progress .progress-bar').progressbar({
        display_text: 'fill'
    });
});

// Lista de BINs comunes
const commonBins = {
    visa: [
        '4532', '4539', '4556', '4916', '4917', '4929', '4532', '4539', '4556', '4916',
        '4917', '4929', '4532', '4539', '4556', '4916', '4917', '4929', '4532', '4539'
    ],
    mastercard: [
        '5104', '5105', '5106', '5107', '5108', '5109', '5110', '5111', '5112', '5113',
        '5114', '5115', '5116', '5117', '5118', '5119', '5120', '5121', '5122', '5123'
    ],
    amex: [
        '3400', '3401', '3402', '3403', '3404', '3405', '3406', '3407', '3408', '3409',
        '3410', '3411', '3412', '3413', '3414', '3415', '3416', '3417', '3418', '3419'
    ]
};

// Función para generar un BIN aleatorio
function generateRandomBin() {
    const cardTypes = Object.keys(commonBins);
    const randomType = cardTypes[Math.floor(Math.random() * cardTypes.length)];
    const bins = commonBins[randomType];
    return bins[Math.floor(Math.random() * bins.length)];
}

// Función para generar un número de tarjeta completo
function generateCardNumber(bin) {
    let cardNumber = '';
    // Reemplazar cada 'x' por un dígito aleatorio
    for (let i = 0; i < bin.length; i++) {
        if (bin[i].toLowerCase() === 'x') {
            cardNumber += Math.floor(Math.random() * 10);
        } else {
            cardNumber += bin[i];
        }
    }
    // Si es Amex (comienza con 3), generar 15 dígitos
    const targetLength = bin.startsWith('3') ? 15 : 16;
    while (cardNumber.length < targetLength) {
        cardNumber += Math.floor(Math.random() * 10);
    }
    return cardNumber;
}

// Función para generar un mes aleatorio
function generateRandomMonth() {
    return String(Math.floor(Math.random() * 12) + 1).padStart(2, '0');
}

// Función para generar un año aleatorio
function generateRandomYear() {
    const currentYear = new Date().getFullYear();
    return String(Math.floor(Math.random() * 10) + currentYear);
}

// Función para generar un CVV aleatorio
function generateRandomCVV(bin) {
    // Si el BIN comienza con 3, es una Amex y necesita CVV de 4 dígitos
    if (bin && bin.startsWith('3')) {
        return String(Math.floor(Math.random() * 9999) + 1).padStart(4, '0');
    }
    // Para otras tarjetas, CVV de 3 dígitos
    return String(Math.floor(Math.random() * 999) + 1).padStart(3, '0');
}

// Función para generar una tarjeta completa
function generateFullCard(bin = null) {
    const cardBin = bin || generateRandomBin();
    const cardNumber = generateCardNumber(cardBin);
    const month = generateRandomMonth();
    const year = generateRandomYear();
    const cvv = generateRandomCVV(cardBin);
    
    return `${cardNumber}|${month}|${year}|${cvv}`;
}

// Función para generar múltiples tarjetas
function generateMultipleCards(quantity, bin = null) {
    let cards = [];
    for (let i = 0; i < quantity; i++) {
        cards.push(generateFullCard(bin));
    }
    return cards;
}

// Evento para el botón de generación
document.addEventListener('DOMContentLoaded', function() {
    const generateButton = document.querySelector('button[onclick="genCC();"]');
    if (generateButton) {
        generateButton.addEventListener('click', function() {
            const bin = document.getElementById('bin').value;
            const quantity = parseInt(document.getElementById('cantidad').value) || 10;
            const cards = generateMultipleCards(quantity, bin);
            document.getElementById('result').value = cards.join('\n');
            
            // Actualizar estadísticas
            if (typeof updateStats === 'function') {
                totalCards = cards.length;
                updateStats();
            }
        });
    }
});