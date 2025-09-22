<?php $site_page = "Amazon Gate"; $path = $_SERVER["DOCUMENT_ROOT"]; include($path."/static/v4/plugins/form/header.php");
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
      <source src="/static/v4/sounds/iniciar.mp3" type="audio/mpeg">
   </audio>
   <audio id="detectaLive">
      <source src="/static/v4/sounds/live.mp3" type="audio/mpeg">
   </audio>
   <div class="content__inner">
      <div class="card">
         <div class="card-body">
            <h4 class="card-title"><i class="zwicon-credit-card"></i> Amazon Gate <span class="badge-shadow badge-primary">Live <span class="badge-shadow badge-pill badge-light">4</span></span> <span class="badge-shadow badge-danger">Die <span class="badge-shadow badge-pill badge-light">0</span></span></h4>
            <div class="row">
               <input type="hidden" name="csrf_token" id="csrf_token" value="" />
               <div class="col-md-3">
                  <!-- Selector de Gateway -->
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="zwicon-credit-card"></i>&nbsp;Gateway</span>
                        </div>
                        <select id="gateway" name="gateway" class="form-control">
                          <option value="">Seleccionar Gateway</option>
                          <option value="amazon">Amazon</option>
                          <option value="stripe">Stripe</option>
                          <option value="paypal">PayPal</option>
                          <option value="chase">Chase</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <!-- Fin selector de Gateway -->
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
                              <option value="rnd"><i class="zwicon-credit-card"></i> Month</option>
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
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-credit-card"></i>&nbsp;Cookie</span>
                           </div>
                           <input id="cookie-input-2" type="text" class="form-control" placeholder="Ingresa la cookie de Amazon">
                        </div>
                     </div>
                     <!-- Selector de país solo para Amazon -->
                     <div class="col-sm-12" id="amazon-options" style="display:none;">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text">País</span>
                           </div>
                           <select class="form-control" id="pais">
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
                     </div>
                  </div>
               </div>
               <div class="col-md-9">
                  <div class="input-group mb-3">
                     <textarea class="form-control" id="result" rows="10" placeholder="415233055156856034|02|2019|227"></textarea>
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
                     <button type="button" id="chk-pause" class="btn btn-theme btn-sm btn--icon-text" style="min-width: 80px; background:#111; color:#fff; border:none;"> <i class="fas fa-pause"></i> Pausar</button>
                     <button type="button" id="chk-stop" class="btn btn-theme btn-sm btn--icon-text" style="min-width: 80px; background:#111; color:#fff; border:none;"> <i class="fas fa-stop"></i> Detener</button>
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
                     <div id="loader">
                        <img src="/static/v4/img/loading2.gif" style="width:30px; height: 30px;" />
                     </div>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="col-md-12">
                     <div class="progress progress-striped text-center" id="barra_porcentaje" style="background-color: #0a2624; display: block;">
                        <div class="progress-bar progress-bar-success" id="porcentaje" style="background-color: black; width: 100%; display: block;">Bienvenido a Amazon Gate!</div>
                     </div>
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
<script src="/static/v4/js/generar.js"></script>
<script src="/static/v4/js/chk.js"></script>
<script src="/static/v4/js/amazon.js"></script>
<script>
// Mostrar/ocultar selector de país solo si es Amazon
  document.addEventListener('DOMContentLoaded', function() {
    var gateway = document.getElementById('gateway');
    var amazonOptions = document.getElementById('amazon-options');
    if(gateway) {
      gateway.addEventListener('change', function() {
        if(this.value === 'amazon') {
          amazonOptions.style.display = 'block';
        } else {
          amazonOptions.style.display = 'none';
        }
      });
      // Mostrar por defecto si ya está seleccionado Amazon
      if(gateway.value === 'amazon') amazonOptions.style.display = 'block';
    }
  });
// Función para enviar notificación a Telegram
function sendTelegramNotification(card, response) {
    const telegramChatId = '<?php echo $telegram_chat_id; ?>';
    if (!telegramChatId) {
        console.log('No hay ID de Telegram configurado');
        return;
    }

    // Crear el objeto con los datos
    const data = {
        card: card,
        gate: 'amazon',
        pais: document.getElementById('pais').value || 'mx',
        response: '✅ VIVA'
    };

    console.log('Enviando notificación:', data); // Debug

    // Enviar la notificación
    fetch('/gates/inc/telegram.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data); // Debug
        if (data.ok) {
            console.log('Notificación enviada correctamente');
        } else {
            console.error('Error al enviar:', data);
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
    });
}

// Modificar la función que maneja las cards Live
document.addEventListener('DOMContentLoaded', function() {
    // Guardar la función original
    const originalAddLive = window.addLive;
    
    // Sobrescribir la función
    window.addLive = function(card, response) {
        console.log('Card Live detectada:', card); // Debug
        // Llamar a la función original
        originalAddLive(card);
        // Enviar notificación
        sendTelegramNotification(card, response);
    };
});
</script>
</body>
</html>
