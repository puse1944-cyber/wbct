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

<style>
/* Estilos personalizados para Amazon Gate - Optimizado */
.amazon-gate-container {
    background: #0a0a0a;
    min-height: 100vh;
    padding: 20px;
}

/* Card principal rediseñada */
.card {
    background: rgba(20, 20, 20, 0.95) !important;
    border: 1px solid #333 !important;
    border-radius: 15px !important;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3) !important;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, transparent, #00ffc4, transparent);
    animation: scanLine 4s infinite;
    will-change: left;
}

.card-title {
    color: #ffffff !important;
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    margin-bottom: 20px !important;
    text-shadow: 0 0 5px rgba(0, 255, 196, 0.3);
    display: flex;
    align-items: center;
    gap: 15px;
}

.card-title i {
    color: #00ffc4;
    font-size: 1.8rem;
    text-shadow: 0 0 8px rgba(0, 255, 196, 0.5);
}

/* Badges rediseñados - Optimizado */
.badge-shadow {
    border-radius: 20px !important;
    padding: 8px 16px !important;
    font-weight: 700 !important;
    text-shadow: none !important;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
}

.badge-primary {
    background: #00ffc4 !important;
    color: #000 !important;
    border: 1px solid #00ffc4 !important;
}

.badge-danger {
    background: #ff4757 !important;
    color: #fff !important;
    border: 1px solid #ff4757 !important;
}

.badge-light {
    background: rgba(255, 255, 255, 0.9) !important;
    color: #000 !important;
    font-weight: 900 !important;
}

/* Input groups rediseñados */
.input-group {
    margin-bottom: 15px;
}

.input-group-text {
    background: rgba(0, 0, 0, 0.8) !important;
    border: 1px solid #333 !important;
    color: #00ffc4 !important;
    font-weight: 600 !important;
    border-right: none !important;
}

.form-control {
    background: rgba(0, 0, 0, 0.8) !important;
    border: 1px solid #333 !important;
    color: #ffffff !important;
    border-left: none !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

.form-control:focus {
    background: rgba(0, 0, 0, 0.9) !important;
    border-color: #00ffc4 !important;
    box-shadow: 0 0 8px rgba(0, 255, 196, 0.2) !important;
    color: #ffffff !important;
}

.form-control::placeholder {
    color: #666 !important;
}

/* Botones rediseñados - Optimizado */
.btn-theme {
    background: #00ffc4 !important;
    border: none !important;
    color: #000 !important;
    font-weight: 700 !important;
    border-radius: 25px !important;
    padding: 12px 25px !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
    box-shadow: 0 3px 8px rgba(0, 255, 196, 0.2) !important;
}

.btn-theme:hover {
    background: #0099ff !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 5px 12px rgba(0, 255, 196, 0.3) !important;
}

.btn-theme:active {
    transform: translateY(0) !important;
}

/* Botones de control rediseñados - Optimizado */
#chk-start, #chk-pause, #chk-stop {
    background: #1a1a1a !important;
    border: 1px solid #333 !important;
    color: #ffffff !important;
    border-radius: 8px !important;
    transition: transform 0.2s ease, background-color 0.2s ease !important;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2) !important;
}

#chk-start:hover {
    background: #00ffc4 !important;
    color: #000 !important;
    border-color: #00ffc4 !important;
    transform: translateY(-1px) !important;
}

#chk-pause:hover {
    background: #ffa502 !important;
    color: #000 !important;
    border-color: #ffa502 !important;
    transform: translateY(-1px) !important;
}

#chk-stop:hover {
    background: #ff4757 !important;
    color: #fff !important;
    border-color: #ff4757 !important;
    transform: translateY(-1px) !important;
}

/* Textarea rediseñada - Optimizado */
#result {
    background: rgba(0, 0, 0, 0.9) !important;
    border: 1px solid #333 !important;
    color: #00ffc4 !important;
    font-family: 'Courier New', monospace !important;
    font-size: 0.9rem !important;
    border-radius: 10px !important;
    padding: 15px !important;
    resize: vertical !important;
    min-height: 200px !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

#result:focus {
    border-color: #00ffc4 !important;
    box-shadow: 0 0 8px rgba(0, 255, 196, 0.2) !important;
}

/* Estadísticas rediseñadas */
.text-center {
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid #333;
    border-radius: 10px;
    padding: 15px;
    margin: 15px 0;
}

.badge {
    border-radius: 15px !important;
    padding: 6px 12px !important;
    font-weight: 700 !important;
    margin: 0 5px !important;
    text-shadow: none !important;
}

.badge-info {
    background: #00ffc4 !important;
    color: #000 !important;
}

.badge-warning {
    background: #ffa502 !important;
    color: #000 !important;
}

.badge-primary {
    background: #00ffc4 !important;
    color: #000 !important;
}

.badge-danger {
    background: #ff4757 !important;
    color: #fff !important;
}

.badge-secondary {
    background: #666 !important;
    color: #fff !important;
}

/* Paneles de resultados rediseñados */
.panel-heading {
    background: rgba(0, 0, 0, 0.8) !important;
    border: 1px solid #333 !important;
    border-radius: 10px 10px 0 0 !important;
    padding: 15px !important;
    margin-bottom: 0 !important;
}

.panel-title {
    color: #ffffff !important;
    font-size: 1.2rem !important;
    font-weight: 700 !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-shadow: 0 0 10px rgba(0, 255, 196, 0.5);
}

.val-lives, .val-dies, .val-errors {
    background: #00ffc4 !important;
    color: #000 !important;
    border: none !important;
    border-radius: 50% !important;
    width: 40px !important;
    height: 40px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 900 !important;
    font-size: 1.1rem !important;
    box-shadow: 0 2px 5px rgba(0, 255, 196, 0.2) !important;
}

/* Áreas de resultados rediseñadas */
#lives, #dies, #errors {
    background: rgba(0, 0, 0, 0.9) !important;
    border: 1px solid #333 !important;
    border-top: none !important;
    border-radius: 0 0 10px 10px !important;
    padding: 15px !important;
    min-height: 200px !important;
    position: relative;
    overflow: hidden;
}

#lives {
    border-left: 3px solid #00ffc4 !important;
}

#lives::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #00ffc4, transparent);
    animation: scanLine 5s infinite;
    will-change: left;
}

#dies {
    border-left: 3px solid #ff4757 !important;
}

#dies::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #ff4757, transparent);
    animation: scanLine 6s infinite;
    will-change: left;
}

#errors {
    border-left: 3px solid #666 !important;
}

#errors::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, #666, transparent);
    animation: scanLine 7s infinite;
    will-change: left;
}

/* Estilos para elementos dentro de las áreas de resultados - Optimizado */
#lives div, #dies div, #errors div {
    background: rgba(0, 0, 0, 0.6) !important;
    border: 1px solid #333 !important;
    border-radius: 8px !important;
    padding: 10px !important;
    margin: 5px 0 !important;
    color: #ffffff !important;
    font-family: 'Courier New', monospace !important;
    font-size: 0.9rem !important;
    transition: transform 0.2s ease, background-color 0.2s ease !important;
    position: relative;
    overflow: hidden;
}

#lives div {
    border-left: 3px solid #00ffc4 !important;
    box-shadow: 0 0 5px rgba(0, 255, 196, 0.1) !important;
}

#lives div:hover {
    background: rgba(0, 255, 196, 0.1) !important;
    transform: translateX(3px) !important;
    box-shadow: 0 0 8px rgba(0, 255, 196, 0.2) !important;
}

#dies div {
    border-left: 3px solid #ff4757 !important;
    box-shadow: 0 0 5px rgba(255, 71, 87, 0.1) !important;
}

#dies div:hover {
    background: rgba(255, 71, 87, 0.1) !important;
    transform: translateX(3px) !important;
    box-shadow: 0 0 8px rgba(255, 71, 87, 0.2) !important;
}

#errors div {
    border-left: 3px solid #666 !important;
    box-shadow: 0 0 5px rgba(102, 102, 102, 0.1) !important;
}

#errors div:hover {
    background: rgba(102, 102, 102, 0.1) !important;
    transform: translateX(3px) !important;
    box-shadow: 0 0 8px rgba(102, 102, 102, 0.2) !important;
}

/* Scrollbar personalizado para las áreas de resultados */
#lives::-webkit-scrollbar, #dies::-webkit-scrollbar, #errors::-webkit-scrollbar {
    width: 6px;
}

#lives::-webkit-scrollbar-track, #dies::-webkit-scrollbar-track, #errors::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 3px;
}

#lives::-webkit-scrollbar-thumb {
    background: #00ffc4;
    border-radius: 3px;
}

#dies::-webkit-scrollbar-thumb {
    background: #ff4757;
    border-radius: 3px;
}

#errors::-webkit-scrollbar-thumb {
    background: #666;
    border-radius: 3px;
}

/* Checkbox rediseñado - Optimizado */
.custom-control-input:checked ~ .custom-control-label::before {
    background: #00ffc4 !important;
    border-color: #00ffc4 !important;
}

.custom-control-label {
    color: #ffffff !important;
    font-weight: 600 !important;
}

/* Note rediseñada */
.note {
    background: rgba(0, 0, 0, 0.8) !important;
    border: 1px solid #333 !important;
    border-radius: 10px !important;
    padding: 15px !important;
}

.note-content h5 {
    color: #00ffc4 !important;
    font-weight: 700 !important;
    margin-bottom: 10px !important;
}

/* Loader rediseñado */
#loader {
    text-align: center;
    padding: 20px;
}

#loader img {
    border-radius: 50%;
    box-shadow: 0 0 20px rgba(0, 255, 196, 0.5);
}

/* Animaciones - Optimizado */
@keyframes scanLine {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Efectos adicionales - Optimizado */
.card-body {
    position: relative;
    z-index: 2;
}

/* Efecto de hover en las cards - Optimizado */
.card:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4) !important;
}

/* Efecto de typing en el textarea - Optimizado */
#result {
    background-image: 
        linear-gradient(90deg, transparent 50%, rgba(0, 255, 196, 0.02) 50%),
        linear-gradient(180deg, transparent 50%, rgba(0, 255, 196, 0.02) 50%);
    background-size: 20px 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .amazon-gate-container {
        padding: 10px;
    }
    
    .card-title {
        font-size: 1.2rem !important;
        flex-direction: column;
        text-align: center;
    }
    
    .btn-theme {
        padding: 10px 20px !important;
        font-size: 0.9rem !important;
    }
    
    .panel-title {
        font-size: 1rem !important;
        flex-direction: column;
        gap: 10px;
    }
    
    .val-lives, .val-dies, .val-errors {
        width: 35px !important;
        height: 35px !important;
        font-size: 1rem !important;
    }
}
</style>
<section class="content">
   <div class="amazon-gate-container">
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
            <h4 class="card-title"><i class="zwicon-credit-card"></i> Amazon Gate <span class="badge-shadow badge-primary">Live <span class="badge-shadow badge-pill badge-light">4</span></span> <span class="badge-shadow badge-danger">Die <span class="badge-shadow badge-pill badge-light">0</span></span></h4>
            <div class="row">
               <input type="hidden" name="csrf_token" id="csrf_token" value="" />
               <div class="col-md-3">
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
                  <div class="col-md-12">
                     <div class="cyberpunk-panel" id="barra_porcentaje">
                        <div class="holographic-display">
                           <div class="status-indicator">
                              <div class="neon-circle"></div>
                              <span class="status-text" id="porcentaje">AMAZON GATE ONLINE</span>
                           </div>
                           <div class="glitch-effect"></div>
                           <div class="scan-lines"></div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="note note-white">
                     <div class="note-content">
                        <h5><b>Estado:</b></h5>
                        <div id="estatus" class="badge badge-info">Whaiting...</div>
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
        response: '✅ LIVE'
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
        
        // Reproducir sonido de live
        try {
            const liveAudio = document.getElementById('detectaLive');
            if (liveAudio) {
                liveAudio.currentTime = 0; // Reiniciar el audio
                liveAudio.play().catch(e => console.log('Error al reproducir sonido de live:', e));
            }
        } catch (e) {
            console.log('Error al reproducir sonido de live:', e);
        }
        
        // Llamar a la función original
        originalAddLive(card);
        // Enviar notificación
        sendTelegramNotification(card, response);
    };
});
</script>
</body>
</html>


                  <div class="input-group mb-3">

                     <textarea class="form-control" id="result" rows="10" placeholder="415233055156856034|02|2019|227"></textarea>

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


   </div>

</section>

</main>

<script>

// Mostrar opciones de Amazon por defecto
document.addEventListener('DOMContentLoaded', function() {
    var amazonOptions = document.getElementById('amazon-options');
    if(amazonOptions) {
        amazonOptions.style.display = 'block';
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

        response: '✅ LIVE'

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

        
        // Reproducir sonido de live
        try {
            const liveAudio = document.getElementById('detectaLive');
            if (liveAudio) {
                liveAudio.currentTime = 0; // Reiniciar el audio
                liveAudio.play().catch(e => console.log('Error al reproducir sonido de live:', e));
            }
        } catch (e) {
            console.log('Error al reproducir sonido de live:', e);
        }

        // Llamar a la función original

        originalAddLive(card);

        // Enviar notificación

        sendTelegramNotification(card, response);

    };

});

</script>

<script src="/static/v4/js/generar.js"></script>
<script src="/static/v4/js/chk.js"></script>
<script src="/static/v4/js/amazon.js"></script>

</body>

</html>


