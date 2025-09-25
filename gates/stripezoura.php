<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate CSRF token
$site_page = "Stripe Gate";
$path = $_SERVER["DOCUMENT_ROOT"];
include($path . "/static/v4/plugins/form/header.php");
require_once $path . "/api/v1.1/core/brain.php";

// Obtener el ID de Telegram del usuario
$user_id = $_SESSION['user_id'];
$stmt = $connection->prepare("SELECT telegram_chat_id FROM breathe_users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$telegram_data = $stmt->fetch(PDO::FETCH_ASSOC);
$telegram_chat_id = $telegram_data['telegram_chat_id'] ?? null;
?>

<section class="content">
   <audio id="iniciaSonido">
      <source src="/static/v4/sounds/iniciar.wav" type="audio/wav">
      <source src="/static/v4/sounds/iniciar.mp3" type="audio/mpeg">
   </audio>
   <audio id="detectaLive">
      <source src="/static/v4/sounds/live.mp3" type="audio/mpeg">
   </audio>
   <div id="toast-container" class="fixed top-5 right-5 z-[101] w-full max-w-xs space-y-3"></div>
   <div class="content__inner">
      <div class="card">
         <div class="card-body">
            <h4 class="card-title"><i class="zwicon-credit-card"></i> Stripe Gate <span class="badge-shadow badge-primary">Live <span class="badge-shadow badge-pill badge-light">0</span></span> <span class="badge-shadow badge-danger">Die <span class="badge-shadow badge-pill badge-light">0</span></span></h4>
            <div class="row">
               <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" />
               <div class="col-md-3">
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-credit-card"></i>&nbsp;Gateway</span>
                           </div>
                           <select id="gateway" name="gateway" class="form-control">
                              <option value="stripe">Stripe</option>
                           </select>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-credit-card"></i>&nbsp;BIN</span>
                           </div>
                           <input id="bin" type="text" class="form-control" placeholder="549627xxxxxxxxxx">
                        </div>
                     </div>
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text">Quantity</span>
                           </div>
                           <input type="number" class="form-control" value="10" id="cantidad">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-calendar"></i></span>
                           </div>
                           <select class="form-control" id="mes">
                              <option value="rnd">Month</option>
                              <option value="01">01</option>
                              <option value="02">02</option>
                              <option value="03">03</option>
                              <option value="04">04</option>
                              <option value="05">05</option>
                              <option value="06">06</option>
                              <option value="07">07</option>
                              <option value="08">08</option>
                              <option value="09">09</option>
                              <option value="10">10</option>
                              <option value="11">11</option>
                              <option value="12">12</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-calendar"></i></span>
                           </div>
                           <select class="form-control" id='anio'>
                              <option value="rnd">Year</option>
                              <option value="2025">2025</option>
                              <option value="2026">2026</option>
                              <option value="2027">2027</option>
                              <option value="2028">2028</option>
                              <option value="2029">2029</option>
                              <option value="2030">2030</option>
                              <option value="2031">2031</option>
                              <option value="2032">2032</option>
                              <option value="2033">2033</option>
                              <option value="2034">2034</option>
                              <option value="2035">2035</option>
                              <option value="2036">2036</option>
                              <option value="2037">2037</option>
                              <option value="2038">2038</option>
                              <option value="2039">2039</option>
                              <option value="2040">2040</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text">CCV2</span>
                           </div>
                           <input type="text" class="form-control" placeholder="rnd" id='cvc'>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <center>
                              <button type="button" onclick="genCC();" class="btn btn-theme"><i class="zwicon-refresh-double"></i> Generate Cards</button>
                           </center>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-9">
                  <div class="input-group mb-3">
                     <textarea class="form-control" id="result" rows="10" placeholder="4830306066267214|06|2032|424"></textarea>
                  </div>
                  <div class="custom-control custom-checkbox mb-2">
                     <input type="checkbox" class="custom-control-input" id="telegramToggle">
                     <label class="custom-control-label" for="telegramToggle">Notificar Lives a Telegram</label>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <div class="d-flex flex-wrap justify-content-center mb-2" style="gap: 6px;">
                     <button type="button" id="chk-start" class="btn btn-theme btn-sm btn--icon-text" style="min-width: 100px; background:#111; color:#fff; border:none;"> <i class="fas fa-play"></i> Iniciar</button>
                     <button type="button" id="chk-stop" class="btn btn-theme btn-sm btn--icon-text" style="min-width: 80px; background:#111; color:#fff; border:none;" disabled> <i class="fas fa-stop"></i> Detener</button>
                     <button type="button" id="chk-clear" class="btn btn-theme btn-sm btn--icon-text" style="min-width: 80px; background:#111; color:#fff; border:none;"> <i class="fas fa-trash-alt"></i> Limpiar</button>
                  </div>
                  <div class="text-center" style="font-size: 13px;">
                     Total: <span id="total" class="badge badge-info">0</span> |
                     Testeadas: <span id="checked" class="badge badge-warning">0</span> |
                     Lives: <span id="livestat" class="badge badge-primary">0</span> |
                     Dies: <span id="diestat" class="badge badge-danger">0</span> |
                     Errores: <span id="errorstat" class="badge badge-secondary">0</span>
                  </div>
               </div>
            </div>
            <br>
            <div class="row">
               <div class="col-md-12">
                  <div id="loader" style="display: none;">
                     <img src="/static/v4/img/loading2.gif" style="width:30px; height: 30px;" />
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="progress progress-striped text-center" id="barra_porcentaje" style="background-color: #0a2624; display: block;">
                     <div class="progress-bar progress-bar-success" id="porcentaje" style="background-color: black; width: 100%; display: block;">Bienvenido a Stripe Gate!</div>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="note note-white">
                     <div class="note-content">
                        <h5><b>Estado:</b></h5>
                        <div id="estatus" class="badge badge-info">A la espera...</div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div id="state_seccion" class="card" data-sortable-id="ui-widget-11">
         <div class="card-body">
            <div class="panel-heading">
               <h4 class="panel-title">
                  <b>Lives</b> 
                  <div class="val-lives btn btn-sm btn-icon btn-circle btn-inverse">0</div>
               </h4>
            </div>
            <div id="lives" class="table-responsive" style="max-height: 300px; overflow-y: auto;"></div>
         </div>
      </div>
      <div class="card" data-sortable-id="ui-widget-11">
         <div class="card-body">
            <div class="panel-heading">
               <h4 class="panel-title">
                  <b>Dies</b> 
                  <div class="val-dies btn btn-sm btn-icon btn-circle btn-inverse">0</div>
               </h4>
            </div>
            <div id="dies" class="table-responsive" style="max-height: 300px; overflow-y: auto;"></div>
         </div>
      </div>
      <div class="card" data-sortable-id="ui-widget-11">
         <div class="card-body">
            <div class="panel-heading">
               <h4 class="panel-title">
                  <b>Errores</b> 
                  <div class="val-errors btn btn-sm btn-icon btn-circle btn-inverse">0</div>
               </h4>
            </div>
            <div id="errors" class="table-responsive" style="max-height: 300px; overflow-y: auto;"></div>
         </div>
      </div>
   </div>
</section>
</main>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="/static/v4/js/generar.js"></script>
<script src="/static/v4/js/chk.js"></script>
<style>
   .toast { animation: slideIn 0.5s ease forwards, slideOut 0.5s ease 3.5s forwards; }
   @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
   @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- ELEMENTOS ---
    const DOMElements = {
        cardList: document.getElementById('result'),
        startButton: document.getElementById('chk-start'),
        stopButton: document.getElementById('chk-stop'),
        clearButton: document.getElementById('chk-clear'),
        totalCount: document.getElementById('total'),
        checkedCount: document.getElementById('checked'),
        liveCount: document.getElementById('livestat'),
        deadCount: document.getElementById('diestat'),
        errorCount: document.getElementById('errorstat'),
        progressBar: document.getElementById('porcentaje'),
        approvedBox: document.getElementById('lives'),
        declinedBox: document.getElementById('dies'),
        errorBox: document.getElementById('errors'),
        loader: document.getElementById('loader'),
        statusText: document.getElementById('estatus'),
        toastContainer: document.getElementById('toast-container'),
        csrfToken: document.getElementById('csrf_token').value
    };

    // --- ESTADO DE LA APP ---
    let state = {
        cards: [],
        currentIndex: 0,
        isChecking: false
    };

    // --- FUNCIONES DE UI ---
    const showToast = (message, type = 'info') => {
        console.log(`Toast: ${message}, Type: ${type}`);
        const toast = document.createElement('div');
        const colors = { success: 'background-color: rgba(34, 197, 94, 0.8);', error: 'background-color: rgba(239, 68, 68, 0.8);' };
        toast.className = 'toast p-4 rounded-lg text-white shadow-lg';
        toast.style = colors[type] || 'background-color: rgba(59, 130, 246, 0.8);';
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i> ${message}`;
        DOMElements.toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.5s ease forwards';
            toast.addEventListener('animationend', () => toast.remove());
        }, 3500);
    };

    const resetUI = () => {
        DOMElements.checkedCount.textContent = '0';
        DOMElements.liveCount.textContent = '0';
        DOMElements.deadCount.textContent = '0';
        DOMElements.errorCount.textContent = '0';
        DOMElements.progressBar.style.width = '100%';
        DOMElements.progressBar.textContent = 'Bienvenido a Stripe Gate!';
        DOMElements.approvedBox.innerHTML = '';
        DOMElements.declinedBox.innerHTML = '';
        DOMElements.errorBox.innerHTML = '';
        DOMElements.startButton.disabled = false;
        DOMElements.stopButton.disabled = true;
        DOMElements.loader.style.display = 'none';
        DOMElements.statusText.textContent = 'A la espera...';
    };

    const updateCountsOnInput = () => {
        DOMElements.totalCount.textContent = DOMElements.cardList.value.split('\n').filter(line => line.trim() !== '').length;
    };

    const addResultToUI = (card, result) => {
        const span = document.createElement('div');
        const statusIcon = result.status === 'success' ? '✅' : '❌';
        span.innerHTML = `${statusIcon} ${card} -> <span>${result.message}</span>`;
        if (result.status === 'success') {
            DOMElements.approvedBox.prepend(span);
            DOMElements.liveCount.textContent = parseInt(DOMElements.liveCount.textContent) + 1;
            document.getElementById('detectaLive').play();
            sendTelegramNotification(card, result.message);
        } else {
            DOMElements.declinedBox.prepend(span);
            DOMElements.deadCount.textContent = parseInt(DOMElements.deadCount.textContent) + 1;
        }

        const checked = parseInt(DOMElements.liveCount.textContent) + parseInt(DOMElements.deadCount.textContent) + parseInt(DOMElements.errorCount.textContent);
        DOMElements.checkedCount.textContent = checked;
        DOMElements.progressBar.style.width = `${state.cards.length > 0 ? (checked / state.cards.length) * 100 : 100}%`;
        DOMElements.progressBar.textContent = `Progreso: ${Math.round(state.cards.length > 0 ? (checked / state.cards.length) * 100 : 100)}%`;
    };

    const finishChecker = (message, type) => {
        state.isChecking = false;
        DOMElements.startButton.disabled = false;
        DOMElements.stopButton.disabled = true;
        DOMElements.loader.style.display = 'none';
        DOMElements.statusText.textContent = type === 'success' ? 'Completado' : 'Detenido';
        showToast(message, type);
    };

    // --- TELEGRAM NOTIFICATIONS ---
    const sendTelegramNotification = async (card, response) => {
        const telegramChatId = '<?php echo $telegram_chat_id; ?>';
        if (!telegramChatId || !document.getElementById('telegramToggle').checked) {
            console.log('Notificación de Telegram desactivada o no configurada');
            return;
        }

        const data = {
            card: card,
            gate: 'stripe',
            response: '✅ VIVA'
        };

        try {
            const response = await fetch('/gates/inc/telegram.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.ok) {
                console.log('Notificación enviada a Telegram');
            } else {
                console.error('Error al enviar notificación:', result);
            }
        } catch (error) {
            console.error('Error en la petición de Telegram:', error);
        }
    };

    // --- LÓGICA DEL CHECKER ---
    const runChecker = async () => {
        if (!state.isChecking) {
            console.log('runChecker detenido: isChecking es false');
            return;
        }

        if (state.currentIndex >= state.cards.length) {
            console.log('runChecker finalizado: no hay más tarjetas');
            finishChecker('¡Proceso completado!', 'success');
            return;
        }

        const card = state.cards[state.currentIndex];
        console.log(`Procesando tarjeta: ${card}, Índice: ${state.currentIndex + 1}/${state.cards.length}`);
        DOMElements.statusText.textContent = `Procesando: ${card.substring(0, 10)}... [${state.currentIndex + 1}/${state.cards.length}]`;

        const cardRegex = /^[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}$/;
        if (!cardRegex.test(card)) {
            console.log(`Tarjeta inválida: ${card}`);
            DOMElements.errorBox.innerHTML = `<div>❌ ${card} -> Formato de tarjeta inválido</div>` + DOMElements.errorBox.innerHTML;
            DOMElements.errorCount.textContent = parseInt(DOMElements.errorCount.textContent) + 1;
            state.currentIndex++;
            setTimeout(runChecker, 2000);
            return;
        }

        const formData = new URLSearchParams();
        formData.append('lista', card);
        formData.append('csrf_token', DOMElements.csrfToken);

        try {
            const response = await fetch('/gates/stripe_process.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Error del servidor: ${response.status}`);
            }

            const result = await response.json();
            console.log(`Respuesta de stripe_process.php: ${JSON.stringify(result)}`);
            addResultToUI(card, result);

        } catch (error) {
            console.log(`Error en solicitud: ${error.message}`);
            DOMElements.errorBox.innerHTML = `<div>❌ ${card} -> Error: ${error.message}</div>` + DOMElements.errorBox.innerHTML;
            DOMElements.errorCount.textContent = parseInt(DOMElements.errorCount.textContent) + 1;
        } finally {
            state.currentIndex++;
            setTimeout(runChecker, 4000); // 4-second delay to match working code
        }
    };

    // --- EVENTOS ---
    DOMElements.cardList.addEventListener('input', updateCountsOnInput);

    DOMElements.clearButton.addEventListener('click', () => {
        console.log('Botón Limpiar clicado');
        DOMElements.cardList.value = '';
        resetUI();
        updateCountsOnInput();
        showToast('Formulario limpiado.', 'info');
    });

    DOMElements.stopButton.addEventListener('click', () => {
        console.log('Botón Detener clicado');
        state.isChecking = false;
        finishChecker('Proceso detenido por el usuario.', 'info');
    });

    DOMElements.startButton.addEventListener('click', () => {
        console.log('Botón Iniciar clicado');
        state.cards = DOMElements.cardList.value.split('\n').filter(line => line.trim() !== '');

        if (state.cards.length === 0) {
            console.log('No se ingresaron tarjetas');
            showToast('Por favor, ingresa al menos una tarjeta.', 'error');
            return;
        }

        const validCards = state.cards.filter(card => /^[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}$/.test(card));
        if (validCards.length === 0) {
            console.log('Ninguna tarjeta tiene formato válido');
            showToast('Ninguna tarjeta tiene un formato válido.', 'error');
            return;
        }

        console.log(`Iniciando chequeo con ${state.cards.length} tarjetas`);
        resetUI();
        DOMElements.totalCount.textContent = state.cards.length;
        DOMElements.startButton.disabled = true;
        DOMElements.stopButton.disabled = false;
        DOMElements.loader.style.display = 'block';
        DOMElements.statusText.textContent = 'Procesando...';
        document.getElementById('iniciaSonido').play();

        state.isChecking = true;
        state.currentIndex = 0;
        runChecker();
    });

    // Inicializar estado
    updateCountsOnInput();
});
</script>
</body>
</html>
        stopButton: document.getElementById('chk-stop'),

        clearButton: document.getElementById('chk-clear'),

        totalCount: document.getElementById('total'),

        checkedCount: document.getElementById('checked'),

        liveCount: document.getElementById('livestat'),

        deadCount: document.getElementById('diestat'),

        errorCount: document.getElementById('errorstat'),

        progressBar: document.getElementById('porcentaje'),

        approvedBox: document.getElementById('lives'),

        declinedBox: document.getElementById('dies'),

        errorBox: document.getElementById('errors'),

        loader: document.getElementById('loader'),

        statusText: document.getElementById('estatus'),

        toastContainer: document.getElementById('toast-container'),

        csrfToken: document.getElementById('csrf_token').value

    };



    // --- ESTADO DE LA APP ---

    let state = {

        cards: [],

        currentIndex: 0,

        isChecking: false

    };



    // --- FUNCIONES DE UI ---

    const showToast = (message, type = 'info') => {

        console.log(`Toast: ${message}, Type: ${type}`);

        const toast = document.createElement('div');

        const colors = { success: 'background-color: rgba(34, 197, 94, 0.8);', error: 'background-color: rgba(239, 68, 68, 0.8);' };

        toast.className = 'toast p-4 rounded-lg text-white shadow-lg';

        toast.style = colors[type] || 'background-color: rgba(59, 130, 246, 0.8);';

        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i> ${message}`;

        DOMElements.toastContainer.appendChild(toast);

        setTimeout(() => {

            toast.style.animation = 'slideOut 0.5s ease forwards';

            toast.addEventListener('animationend', () => toast.remove());

        }, 3500);

    };



    const resetUI = () => {

        DOMElements.checkedCount.textContent = '0';

        DOMElements.liveCount.textContent = '0';

        DOMElements.deadCount.textContent = '0';

        DOMElements.errorCount.textContent = '0';

        DOMElements.progressBar.style.width = '100%';

        DOMElements.progressBar.textContent = 'Bienvenido a Stripe Gate!';

        DOMElements.approvedBox.innerHTML = '';

        DOMElements.declinedBox.innerHTML = '';

        DOMElements.errorBox.innerHTML = '';

        DOMElements.startButton.disabled = false;

        DOMElements.stopButton.disabled = true;

        DOMElements.loader.style.display = 'none';

        DOMElements.statusText.textContent = 'A la espera...';

    };



    const updateCountsOnInput = () => {

        DOMElements.totalCount.textContent = DOMElements.cardList.value.split('\n').filter(line => line.trim() !== '').length;

    };



    const addResultToUI = (card, result) => {

        const span = document.createElement('div');

        const statusIcon = result.status === 'success' ? '✅' : '❌';

        span.innerHTML = `${statusIcon} ${card} -> <span>${result.message}</span>`;

        if (result.status === 'success') {

            DOMElements.approvedBox.prepend(span);

            DOMElements.liveCount.textContent = parseInt(DOMElements.liveCount.textContent) + 1;

            document.getElementById('detectaLive').play();

            sendTelegramNotification(card, result.message);

        } else {

            DOMElements.declinedBox.prepend(span);

            DOMElements.deadCount.textContent = parseInt(DOMElements.deadCount.textContent) + 1;

        }



        const checked = parseInt(DOMElements.liveCount.textContent) + parseInt(DOMElements.deadCount.textContent) + parseInt(DOMElements.errorCount.textContent);

        DOMElements.checkedCount.textContent = checked;

        DOMElements.progressBar.style.width = `${state.cards.length > 0 ? (checked / state.cards.length) * 100 : 100}%`;

        DOMElements.progressBar.textContent = `Progreso: ${Math.round(state.cards.length > 0 ? (checked / state.cards.length) * 100 : 100)}%`;

    };



    const finishChecker = (message, type) => {

        state.isChecking = false;

        DOMElements.startButton.disabled = false;

        DOMElements.stopButton.disabled = true;

        DOMElements.loader.style.display = 'none';

        DOMElements.statusText.textContent = type === 'success' ? 'Completado' : 'Detenido';

        showToast(message, type);

    };



    // --- TELEGRAM NOTIFICATIONS ---

    const sendTelegramNotification = async (card, response) => {

        const telegramChatId = '<?php echo $telegram_chat_id; ?>';

        if (!telegramChatId || !document.getElementById('telegramToggle').checked) {

            console.log('Notificación de Telegram desactivada o no configurada');

            return;

        }



        const data = {

            card: card,

            gate: 'stripe',

            response: '✅ VIVA'

        };



        try {

            const response = await fetch('/gates/inc/telegram.php', {

                method: 'POST',

                headers: { 'Content-Type': 'application/json' },

                body: JSON.stringify(data)

            });

            const result = await response.json();

            if (result.ok) {

                console.log('Notificación enviada a Telegram');

            } else {

                console.error('Error al enviar notificación:', result);

            }

        } catch (error) {

            console.error('Error en la petición de Telegram:', error);

        }

    };



    // --- LÓGICA DEL CHECKER ---

    const runChecker = async () => {

        if (!state.isChecking) {

            console.log('runChecker detenido: isChecking es false');

            return;

        }



        if (state.currentIndex >= state.cards.length) {

            console.log('runChecker finalizado: no hay más tarjetas');

            finishChecker('¡Proceso completado!', 'success');

            return;

        }



        const card = state.cards[state.currentIndex];

        console.log(`Procesando tarjeta: ${card}, Índice: ${state.currentIndex + 1}/${state.cards.length}`);

        DOMElements.statusText.textContent = `Procesando: ${card.substring(0, 10)}... [${state.currentIndex + 1}/${state.cards.length}]`;



        const cardRegex = /^[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}$/;

        if (!cardRegex.test(card)) {

            console.log(`Tarjeta inválida: ${card}`);

            DOMElements.errorBox.innerHTML = `<div>❌ ${card} -> Formato de tarjeta inválido</div>` + DOMElements.errorBox.innerHTML;

            DOMElements.errorCount.textContent = parseInt(DOMElements.errorCount.textContent) + 1;

            state.currentIndex++;

            setTimeout(runChecker, 2000);

            return;

        }



        const formData = new URLSearchParams();

        formData.append('lista', card);

        formData.append('csrf_token', DOMElements.csrfToken);



        try {

            const response = await fetch('/gates/stripe_process.php', {

                method: 'POST',

                body: formData

            });



            if (!response.ok) {

                throw new Error(`Error del servidor: ${response.status}`);

            }



            const result = await response.json();

            console.log(`Respuesta de stripe_process.php: ${JSON.stringify(result)}`);

            addResultToUI(card, result);



        } catch (error) {

            console.log(`Error en solicitud: ${error.message}`);

            DOMElements.errorBox.innerHTML = `<div>❌ ${card} -> Error: ${error.message}</div>` + DOMElements.errorBox.innerHTML;

            DOMElements.errorCount.textContent = parseInt(DOMElements.errorCount.textContent) + 1;

        } finally {

            state.currentIndex++;

            setTimeout(runChecker, 4000); // 4-second delay to match working code

        }

    };



    // --- EVENTOS ---

    DOMElements.cardList.addEventListener('input', updateCountsOnInput);



    DOMElements.clearButton.addEventListener('click', () => {

        console.log('Botón Limpiar clicado');

        DOMElements.cardList.value = '';

        resetUI();

        updateCountsOnInput();

        showToast('Formulario limpiado.', 'info');

    });



    DOMElements.stopButton.addEventListener('click', () => {

        console.log('Botón Detener clicado');

        state.isChecking = false;

        finishChecker('Proceso detenido por el usuario.', 'info');

    });



    DOMElements.startButton.addEventListener('click', () => {

        console.log('Botón Iniciar clicado');

        state.cards = DOMElements.cardList.value.split('\n').filter(line => line.trim() !== '');



        if (state.cards.length === 0) {

            console.log('No se ingresaron tarjetas');

            showToast('Por favor, ingresa al menos una tarjeta.', 'error');

            return;

        }



        const validCards = state.cards.filter(card => /^[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}$/.test(card));

        if (validCards.length === 0) {

            console.log('Ninguna tarjeta tiene formato válido');

            showToast('Ninguna tarjeta tiene un formato válido.', 'error');

            return;

        }



        console.log(`Iniciando chequeo con ${state.cards.length} tarjetas`);

        resetUI();

        DOMElements.totalCount.textContent = state.cards.length;

        DOMElements.startButton.disabled = true;

        DOMElements.stopButton.disabled = false;

        DOMElements.loader.style.display = 'block';

        DOMElements.statusText.textContent = 'Procesando...';

        document.getElementById('iniciaSonido').play();



        state.isChecking = true;

        state.currentIndex = 0;

        runChecker();

    });



    // Inicializar estado

    updateCountsOnInput();

});

</script>

</body>

</html>

        stopButton: document.getElementById('chk-stop'),

        clearButton: document.getElementById('chk-clear'),

        totalCount: document.getElementById('total'),

        checkedCount: document.getElementById('checked'),

        liveCount: document.getElementById('livestat'),

        deadCount: document.getElementById('diestat'),

        errorCount: document.getElementById('errorstat'),

        progressBar: document.getElementById('porcentaje'),

        approvedBox: document.getElementById('lives'),

        declinedBox: document.getElementById('dies'),

        errorBox: document.getElementById('errors'),

        loader: document.getElementById('loader'),

        statusText: document.getElementById('estatus'),

        toastContainer: document.getElementById('toast-container'),

        csrfToken: document.getElementById('csrf_token').value

    };



    // --- ESTADO DE LA APP ---

    let state = {

        cards: [],

        currentIndex: 0,

        isChecking: false

    };



    // --- FUNCIONES DE UI ---

    const showToast = (message, type = 'info') => {

        console.log(`Toast: ${message}, Type: ${type}`);

        const toast = document.createElement('div');

        const colors = { success: 'background-color: rgba(34, 197, 94, 0.8);', error: 'background-color: rgba(239, 68, 68, 0.8);' };

        toast.className = 'toast p-4 rounded-lg text-white shadow-lg';

        toast.style = colors[type] || 'background-color: rgba(59, 130, 246, 0.8);';

        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i> ${message}`;

        DOMElements.toastContainer.appendChild(toast);

        setTimeout(() => {

            toast.style.animation = 'slideOut 0.5s ease forwards';

            toast.addEventListener('animationend', () => toast.remove());

        }, 3500);

    };



    const resetUI = () => {

        DOMElements.checkedCount.textContent = '0';

        DOMElements.liveCount.textContent = '0';

        DOMElements.deadCount.textContent = '0';

        DOMElements.errorCount.textContent = '0';

        DOMElements.progressBar.style.width = '100%';

        DOMElements.progressBar.textContent = 'Bienvenido a Stripe Gate!';

        DOMElements.approvedBox.innerHTML = '';

        DOMElements.declinedBox.innerHTML = '';

        DOMElements.errorBox.innerHTML = '';

        DOMElements.startButton.disabled = false;

        DOMElements.stopButton.disabled = true;

        DOMElements.loader.style.display = 'none';

        DOMElements.statusText.textContent = 'A la espera...';

    };



    const updateCountsOnInput = () => {

        DOMElements.totalCount.textContent = DOMElements.cardList.value.split('\n').filter(line => line.trim() !== '').length;

    };



    const addResultToUI = (card, result) => {

        const span = document.createElement('div');

        const statusIcon = result.status === 'success' ? '✅' : '❌';

        span.innerHTML = `${statusIcon} ${card} -> <span>${result.message}</span>`;

        if (result.status === 'success') {

            DOMElements.approvedBox.prepend(span);

            DOMElements.liveCount.textContent = parseInt(DOMElements.liveCount.textContent) + 1;

            document.getElementById('detectaLive').play();

            sendTelegramNotification(card, result.message);

        } else {

            DOMElements.declinedBox.prepend(span);

            DOMElements.deadCount.textContent = parseInt(DOMElements.deadCount.textContent) + 1;

        }



        const checked = parseInt(DOMElements.liveCount.textContent) + parseInt(DOMElements.deadCount.textContent) + parseInt(DOMElements.errorCount.textContent);

        DOMElements.checkedCount.textContent = checked;

        DOMElements.progressBar.style.width = `${state.cards.length > 0 ? (checked / state.cards.length) * 100 : 100}%`;

        DOMElements.progressBar.textContent = `Progreso: ${Math.round(state.cards.length > 0 ? (checked / state.cards.length) * 100 : 100)}%`;

    };



    const finishChecker = (message, type) => {

        state.isChecking = false;

        DOMElements.startButton.disabled = false;

        DOMElements.stopButton.disabled = true;

        DOMElements.loader.style.display = 'none';

        DOMElements.statusText.textContent = type === 'success' ? 'Completado' : 'Detenido';

        showToast(message, type);

    };



    // --- TELEGRAM NOTIFICATIONS ---

    const sendTelegramNotification = async (card, response) => {

        const telegramChatId = '<?php echo $telegram_chat_id; ?>';

        if (!telegramChatId || !document.getElementById('telegramToggle').checked) {

            console.log('Notificación de Telegram desactivada o no configurada');

            return;

        }



        const data = {

            card: card,

            gate: 'stripe',

            response: '✅ VIVA'

        };



        try {

            const response = await fetch('/gates/inc/telegram.php', {

                method: 'POST',

                headers: { 'Content-Type': 'application/json' },

                body: JSON.stringify(data)

            });

            const result = await response.json();

            if (result.ok) {

                console.log('Notificación enviada a Telegram');

            } else {

                console.error('Error al enviar notificación:', result);

            }

        } catch (error) {

            console.error('Error en la petición de Telegram:', error);

        }

    };



    // --- LÓGICA DEL CHECKER ---

    const runChecker = async () => {

        if (!state.isChecking) {

            console.log('runChecker detenido: isChecking es false');

            return;

        }



        if (state.currentIndex >= state.cards.length) {

            console.log('runChecker finalizado: no hay más tarjetas');

            finishChecker('¡Proceso completado!', 'success');

            return;

        }



        const card = state.cards[state.currentIndex];

        console.log(`Procesando tarjeta: ${card}, Índice: ${state.currentIndex + 1}/${state.cards.length}`);

        DOMElements.statusText.textContent = `Procesando: ${card.substring(0, 10)}... [${state.currentIndex + 1}/${state.cards.length}]`;



        const cardRegex = /^[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}$/;

        if (!cardRegex.test(card)) {

            console.log(`Tarjeta inválida: ${card}`);

            DOMElements.errorBox.innerHTML = `<div>❌ ${card} -> Formato de tarjeta inválido</div>` + DOMElements.errorBox.innerHTML;

            DOMElements.errorCount.textContent = parseInt(DOMElements.errorCount.textContent) + 1;

            state.currentIndex++;

            setTimeout(runChecker, 2000);

            return;

        }



        const formData = new URLSearchParams();

        formData.append('lista', card);

        formData.append('csrf_token', DOMElements.csrfToken);



        try {

            const response = await fetch('/gates/stripe_process.php', {

                method: 'POST',

                body: formData

            });



            if (!response.ok) {

                throw new Error(`Error del servidor: ${response.status}`);

            }



            const result = await response.json();

            console.log(`Respuesta de stripe_process.php: ${JSON.stringify(result)}`);

            addResultToUI(card, result);



        } catch (error) {

            console.log(`Error en solicitud: ${error.message}`);

            DOMElements.errorBox.innerHTML = `<div>❌ ${card} -> Error: ${error.message}</div>` + DOMElements.errorBox.innerHTML;

            DOMElements.errorCount.textContent = parseInt(DOMElements.errorCount.textContent) + 1;

        } finally {

            state.currentIndex++;

            setTimeout(runChecker, 4000); // 4-second delay to match working code

        }

    };



    // --- EVENTOS ---

    DOMElements.cardList.addEventListener('input', updateCountsOnInput);



    DOMElements.clearButton.addEventListener('click', () => {

        console.log('Botón Limpiar clicado');

        DOMElements.cardList.value = '';

        resetUI();

        updateCountsOnInput();

        showToast('Formulario limpiado.', 'info');

    });



    DOMElements.stopButton.addEventListener('click', () => {

        console.log('Botón Detener clicado');

        state.isChecking = false;

        finishChecker('Proceso detenido por el usuario.', 'info');

    });



    DOMElements.startButton.addEventListener('click', () => {

        console.log('Botón Iniciar clicado');

        state.cards = DOMElements.cardList.value.split('\n').filter(line => line.trim() !== '');



        if (state.cards.length === 0) {

            console.log('No se ingresaron tarjetas');

            showToast('Por favor, ingresa al menos una tarjeta.', 'error');

            return;

        }



        const validCards = state.cards.filter(card => /^[0-9]{15,16}\|[0-9]{2}\|[0-9]{2,4}\|[0-9]{3,4}$/.test(card));

        if (validCards.length === 0) {

            console.log('Ninguna tarjeta tiene formato válido');

            showToast('Ninguna tarjeta tiene un formato válido.', 'error');

            return;

        }



        console.log(`Iniciando chequeo con ${state.cards.length} tarjetas`);

        resetUI();

        DOMElements.totalCount.textContent = state.cards.length;

        DOMElements.startButton.disabled = true;

        DOMElements.stopButton.disabled = false;

        DOMElements.loader.style.display = 'block';

        DOMElements.statusText.textContent = 'Procesando...';

        document.getElementById('iniciaSonido').play();



        state.isChecking = true;

        state.currentIndex = 0;

        runChecker();

    });



    // Inicializar estado

    updateCountsOnInput();

});

</script>

</body>

</html>
