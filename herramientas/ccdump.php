<?php $site_page = "CCDump"; $path = $_SERVER["DOCUMENT_ROOT"]; include($path."/static/v4/plugins/form/header.php"); ?>
<section class="content">
   <div class="col-lg-12">
   <div class="panel panel-default" data-sortable-id="table-basic-7">
      <div class="panel-heading">
         <h3> <i class="ti ti-cloud"></i> CCs - Cloud Storage</h3>
         <h6 class="panel-title">Use estas CCs para sacar nuevas extrapolaciones ─ AVISO: Solo sirven para extrapolar</h6>
      </div>
      <div class="panel-body threads">
         <div class="form-inline m-b-0">
            <label for="binname">Search BIN: </label> &nbsp;
            <input type="text" onkeyup="buscar_ahora($('#buscador').val());" class="form-control" id="buscador" name="buscador" placeholder="Insert BIN here!" maxlength="6">
                  </div>
                  <hr>
                  <div class="table-responsive">
                     <table class="table table-striped m-b-0">
                        <thead>
                           <tr>
                              <!-- <th>[Credit Card] | [Gate] | [Date]</th> -->
                              <!-- <th>Credit Card</th> -->
                              <!-- <th>Fecha</th> -->
                              <!-- <th>Opciones</th> -->
                           </tr>
                        </thead>
                        <tbody id="credits" style="background-color: #0000FF;">
                        <div id="cover-spin"></div>
                        </tbody>
                  </div>
               </div>
            </div>
         </div>
      <script src="/static/v4/js/breathe.js"></script>
   </body>
</html>