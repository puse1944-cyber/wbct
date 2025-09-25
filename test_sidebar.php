<?php $site_page = "Test Sidebar"; $path = $_SERVER["DOCUMENT_ROOT"]; include($path."/static/v4/plugins/form/header.php"); ?>
<section class="content">
   <div class="content__inner">
      <div class="card">
         <div class="card-body">
            <h4 class="card-title"><i class="zwicon-credit-card"></i> Test Sidebar</h4>
            <p class="card-text">Esta página es para probar que el sidebar se actualice correctamente.</p>
            <p class="card-text">Si ves los nuevos enlaces de gates en el sidebar, los cambios están funcionando.</p>
            
            <div class="alert alert-info">
               <h5>Enlaces que deberías ver en el sidebar:</h5>
               <ul>
                  <li>🏠 Home</li>
                  <li>🎯 Gates Hub</li>
                  <li>📊 Amazon Gate</li>
                  <li>💳 Stripe Gate</li>
                  <li>🅿️ PayPal Gate</li>
                  <li>🏦 Chase Gate</li>
                  <li>🔑 Canjear Key</li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</section>
</main>

</body>
</html>

