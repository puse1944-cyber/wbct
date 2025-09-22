<?php $site_page = "Stripe Gate"; $path = $_SERVER["DOCUMENT_ROOT"]; include($path."/static/v4/plugins/form/header.php"); ?>
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
            <h4 class="card-title"><i class="zwicon-credit-card"></i> Stripe Gate <span class="badge-shadow badge-primary">Live <span class="badge-shadow badge-pill badge-light">4</span></span> <span class="badge-shadow badge-danger">Die <span class="badge-shadow badge-pill badge-light">0</span></span></h4>
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
               </div>
               <div class="col-md-9">
                  <div class="input-group mb-3">
                     <textarea class="form-control" id="result" rows="10" placeholder="415233055156856034|02|2019|227"></textarea>
                  </div>
                  <div class="custom-control custom-checkbox mb-2">
                     <input type="checkbox" class="custom-control-input" id="customCheckDisabled1" disabled="" checked>
                     <label class="custom-control-label" for="customCheckDisabled1">Stop checking if Live found! </label>
                  </div>
                  <div class="custom-control custom-checkbox mb-2">
                     <input type="checkbox" class="custom-control-input" id="customCheckDisabled1" disabled="">
                     <label class="custom-control-label" for="customCheckDisabled1">Anti Re-Check Live|Die! - <i>(Coming Soon)</i></label>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <center>
                     <button type="button" id="iniciar" name="iniciar" class="btn btn-theme btn--icon-text"><i class="fas fa-play"></i> Start Check!</button>
                     &nbsp;&nbsp;&nbsp;&nbsp;
                     <button type="button" id="detener" name="detener" class="btn btn-theme btn--icon-text"><i class="fas fa-stop"></i> Stop Check!</button>
                  </center>
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
                  <div class="form-group text-center" id="datalog" style="display: block;">
                     Total: <span id="total" class="badge-shadow badge-pill badge-info">0</span>| Checked: <span id="checked" class="badge-shadow badge-pill badge-warning">0</span> | Live: <span id="livestat" class="badge-shadow badge-pill badge-primary">0</span> | Die: <span id="diestat" class="badge-shadow badge-pill badge-danger">0</span>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="col-md-12">
                     <div class="progress progress-striped text-center" id="barra_porcentaje" style="background-color: #0a2624; display: block;">
                        <div class="progress-bar progress-bar-success" id="porcentaje" style="background-color: black; width: 100%; display: block;">Welcome to Stripe Gate!</div>
                     </div>
                  </div>
               </div>
               <div class="col-md-12">
                  <div class="note note-white">
                     <div class="note-content">
                        <h5><b>Status:</b></h5>
                        <div id="state">A la espera...</div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Contenedores para listas de resultados -->
            <div class="row">
               <div class="col-md-12">
                  <div class="card">
                     <div class="card-body">
                        <div class="panel-heading">
                           <h4 class="panel-title">
                              <b>Lives</b> 
                              <div id="stripe-lives-count" class="val-lives btn btn-sm btn-icon btn-circle btn-inverse">0</div>
                           </h4>
                        </div>
                        <div id="stripe-lives" class="table-responsive" style="max-height: 120px; overflow-y: auto;"></div>
                     </div>
                  </div>
                  <div class="card">
                     <div class="card-body">
                        <div class="panel-heading">
                           <h4 class="panel-title">
                              <b>Dies</b> 
                              <div id="stripe-dies-count" class="val-dies btn btn-sm btn-icon btn-circle btn-inverse">0</div>
                           </h4>
                        </div>
                        <div id="stripe-dies" class="table-responsive" style="max-height: 120px; overflow-y: auto;"></div>
                     </div>
                  </div>
                  <div class="card">
                     <div class="card-body">
                        <div class="panel-heading">
                           <h4 class="panel-title">
                              <b>Errores</b> 
                              <div id="stripe-errors-count" class="val-errors btn btn-sm btn-icon btn-circle btn-inverse">0</div>
                           </h4>
                        </div>
                        <div id="stripe-errors" class="table-responsive" style="max-height: 120px; overflow-y: auto;"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script src="/static/v4/js/generar.js"></script>
<script>
let isChecking = false;
let totalCards = 0;
let checkedCards = 0;
let liveCards = 0;
let dieCards = 0;
let stripeLivesNum = 0;
let stripeDiesNum = 0;
let stripeErrorsNum = 0;

function updateStats() {
    document.getElementById('total').textContent = totalCards;
    document.getElementById('checked').textContent = checkedCards;
    document.getElementById('livestat').textContent = liveCards;
    document.getElementById('diestat').textContent = dieCards;
}

function addToStripeList(type, card, result) {
    let html = `<span style='color:#00fff7;font-weight:bold;'>${card}</span> <span style='color:#fff;'>➔ ${result}</span>`;
    if(type === 'live') {
        stripeLivesNum++;
        document.getElementById('stripe-lives').innerHTML += `<div style='margin-bottom:2px;'>${html}</div>`;
        document.getElementById('stripe-lives-count').textContent = stripeLivesNum;
    } else if(type === 'die') {
        stripeDiesNum++;
        document.getElementById('stripe-dies').innerHTML += `<div style='margin-bottom:2px;'>${html}</div>`;
        document.getElementById('stripe-dies-count').textContent = stripeDiesNum;
    } else {
        stripeErrorsNum++;
        document.getElementById('stripe-errors').innerHTML += `<div style='margin-bottom:2px;'>${html}</div>`;
        document.getElementById('stripe-errors-count').textContent = stripeErrorsNum;
    }
}
</script>

<?php include($path."/static/v4/plugins/form/footer.php"); ?>
