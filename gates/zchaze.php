<?php $site_page = "ZChaze Gate"; $path = $_SERVER["DOCUMENT_ROOT"]; include($path."/static/v4/plugins/form/header.php"); ?>
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
            <h4 class="card-title"><i class="zwicon-credit-card"></i> ZChaze Gate <span class="badge-shadow badge-primary">Live <span class="badge-shadow badge-pill badge-light">4</span></span> <span class="badge-shadow badge-danger">Die <span class="badge-shadow badge-pill badge-light">0</span></span></h4>
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
            <div class="d-flex flex-wrap justify-content-center mb-2" style="gap: 6px;">
               <button type="button" id="iniciar" name="iniciar" class="btn btn-theme btn-sm btn--icon-text" style="min-width: 100px; background:#111; color:#fff; border:none;"><i class="fas fa-play"></i> Iniciar</button>
               <button type="button" id="detener" name="detener" class="btn btn-theme btn-sm btn--icon-text" style="min-width: 80px; background:#111; color:#fff; border:none;"><i class="fas fa-stop"></i> Detener</button>
            </div>
            <div class="text-center" style="font-size: 13px;">
               Total: <span id="total" class="badge badge-info">0</span> |
               Testeadas: <span id="checked" class="badge badge-warning">0</span> |
               Lives: <span id="livestat" class="badge badge-primary">0</span> |
               Dies: <span id="diestat" class="badge badge-danger">0</span> |
               Errores: <span id="errorstat" class="badge badge-secondary">0</span>
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
                        <div class="progress-bar progress-bar-success" id="porcentaje" style="background-color: black; width: 100%; display: block;">Welcome to ZChaze Gate!</div>
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
                              <div id="zchaze-lives-count" class="val-lives btn btn-sm btn-icon btn-circle btn-inverse">0</div>
                           </h4>
                        </div>
                        <div id="zchaze-lives" class="table-responsive" style="max-height: 120px; overflow-y: auto;"></div>
                     </div>
                  </div>
                  <div class="card">
                     <div class="card-body">
                        <div class="panel-heading">
                           <h4 class="panel-title">
                              <b>Dies</b> 
                              <div id="zchaze-dies-count" class="val-dies btn btn-sm btn-icon btn-circle btn-inverse">0</div>
                           </h4>
                        </div>
                        <div id="zchaze-dies" class="table-responsive" style="max-height: 120px; overflow-y: auto;"></div>
                     </div>
                  </div>
                  <div class="card">
                     <div class="card-body">
                        <div class="panel-heading">
                           <h4 class="panel-title">
                              <b>Errores</b> 
                              <div id="zchaze-errors-count" class="val-errors btn btn-sm btn-icon btn-circle btn-inverse">0</div>
                           </h4>
                        </div>
                        <div id="zchaze-errors" class="table-responsive" style="max-height: 120px; overflow-y: auto;"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
let isChecking = false;
let totalCards = 0;
let checkedCards = 0;
let liveCards = 0;
let dieCards = 0;
let zchazeLivesNum = 0;
let zchazeDiesNum = 0;
let zchazeErrorsNum = 0;

function updateStats() {
    document.getElementById('total').textContent = totalCards;
    document.getElementById('checked').textContent = checkedCards;
    document.getElementById('livestat').textContent = liveCards;
    document.getElementById('diestat').textContent = dieCards;
}

function genCC() {
    const bin = document.getElementById('bin').value;
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const mes = document.getElementById('mes').value;
    const anio = document.getElementById('anio').value;
    const cvc = document.getElementById('cvc').value;
    
    let cards = [];
    for (let i = 0; i < cantidad; i++) {
        let cc = bin;
        while (cc.length < 16) {
            cc += Math.floor(Math.random() * 10);
        }
        
        let mm = mes === 'rnd' ? String(Math.floor(Math.random() * 12) + 1).padStart(2, '0') : mes;
        let yy = anio === 'rnd' ? String(Math.floor(Math.random() * 10) + 2025) : anio;
        let cvv = (!cvc || cvc === 'rnd') ? String(Math.floor(Math.random() * 999) + 1).padStart(3, '0') : cvc;
        
        cards.push(`${cc}|${mm}|${yy}|${cvv}`);
    }
    
    document.getElementById('result').value = cards.join('\n');
    totalCards = cards.length;
    updateStats();
}

function addToZChazeList(type, card, result) {
    let html = `<span style='color:#00fff7;font-weight:bold;'>${card}</span> <span style='color:#fff;'>➔ ${result}</span>`;
    if(type === 'live') {
        zchazeLivesNum++;
        document.getElementById('zchaze-lives').innerHTML += `<div style='margin-bottom:2px;'>${html}</div>`;
        document.getElementById('zchaze-lives-count').textContent = zchazeLivesNum;
    } else if(type === 'die') {
        zchazeDiesNum++;
        document.getElementById('zchaze-dies').innerHTML += `<div style='margin-bottom:2px;'>${html}</div>`;
        document.getElementById('zchaze-dies-count').textContent = zchazeDiesNum;
    } else {
        zchazeErrorsNum++;
        document.getElementById('zchaze-errors').innerHTML += `<div style='margin-bottom:2px;'>${html}</div>`;
        document.getElementById('zchaze-errors-count').textContent = zchazeErrorsNum;
    }
}

document.getElementById('iniciar').addEventListener('click', function() {
    if (!isChecking) {
        isChecking = true;
        const cards = document.getElementById('result').value.split('\n').filter(card => card.trim());
        totalCards = cards.length;
        checkedCards = 0;
        liveCards = 0;
        dieCards = 0;
        updateStats();
        
        document.getElementById('loader').style.display = 'block';
        document.getElementById('state').textContent = 'Verificando tarjetas...';
        
        checkCards(cards);
    }
});

document.getElementById('detener').addEventListener('click', function() {
    isChecking = false;
    document.getElementById('loader').style.display = 'none';
    document.getElementById('state').textContent = 'Verificación detenida';
});

async function checkCards(cards) {
    for (let i = 0; i < cards.length && isChecking; i++) {
        const card = cards[i].trim();
        if (!card) continue;
        
        try {
            const response = await fetch(`/gates/zchaze_check.php?card=${encodeURIComponent(card)}`);
            const data = await response.json();
            
            checkedCards++;
            if (data.status1.includes("Aprobada")) {
                liveCards++;
                addToZChazeList('live', card, data.status2);
            } else {
                dieCards++;
                addToZChazeList('die', card, data.status2);
            }
            
            updateStats();
            document.getElementById('porcentaje').style.width = `${(checkedCards / totalCards) * 100}%`;
            document.getElementById('state').textContent = `Verificando tarjeta ${checkedCards} de ${totalCards}`;
            
        } catch (error) {
            addToZChazeList('error', card, 'Error en la verificación');
            checkedCards++;
            updateStats();
        }
    }
    
    isChecking = false;
    document.getElementById('loader').style.display = 'none';
    document.getElementById('state').textContent = 'Verificación completada';
}
</script>
<?php include($path."/static/v4/plugins/form/footer.php"); ?>

