<?php $site_page = "Gates Hub"; $path = $_SERVER["DOCUMENT_ROOT"]; include($path."/static/v4/plugins/form/header.php"); ?>
<section class="content">
   <div class="content__inner">
      <div class="card">
         <div class="card-body">
            <h4 class="card-title"><i class="zwicon-credit-card"></i> Gates Hub</h4>
            <p class="card-text">Selecciona el gate que deseas usar:</p>
            
            <div class="row">
               <div class="col-md-6 col-lg-3 mb-4">
                  <div class="card h-100">
                     <div class="card-body text-center">
                        <i class="zwicon-amazon" style="font-size: 3rem; color: #FF9900; margin-bottom: 1rem;"></i>
                        <h5 class="card-title">Amazon Gate</h5>
                        <p class="card-text">Procesar tarjetas con Amazon</p>
                        <a href="amazon.php" class="btn btn-primary btn-block">
                           <i class="fas fa-arrow-right"></i> Ir a Amazon Gate
                        </a>
                     </div>
                  </div>
               </div>
               
               <div class="col-md-6 col-lg-3 mb-4">
                  <div class="card h-100">
                     <div class="card-body text-center">
                        <i class="zwicon-credit-card" style="font-size: 3rem; color: #635BFF; margin-bottom: 1rem;"></i>
                        <h5 class="card-title">Stripe Gate</h5>
                        <p class="card-text">Procesar tarjetas con Stripe</p>
                        <a href="stripe_gate.php" class="btn btn-primary btn-block">
                           <i class="fas fa-arrow-right"></i> Ir a Stripe Gate
                        </a>
                     </div>
                  </div>
               </div>
               
               <div class="col-md-6 col-lg-3 mb-4">
                  <div class="card h-100">
                     <div class="card-body text-center">
                        <i class="zwicon-paypal" style="font-size: 3rem; color: #0070BA; margin-bottom: 1rem;"></i>
                        <h5 class="card-title">PayPal Gate</h5>
                        <p class="card-text">Procesar tarjetas con PayPal</p>
                        <a href="paypal_gate.php" class="btn btn-primary btn-block">
                           <i class="fas fa-arrow-right"></i> Ir a PayPal Gate
                        </a>
                     </div>
                  </div>
               </div>
               
               <div class="col-md-6 col-lg-3 mb-4">
                  <div class="card h-100">
                     <div class="card-body text-center">
                        <i class="zwicon-credit-card" style="font-size: 3rem; color: #0066B2; margin-bottom: 1rem;"></i>
                        <h5 class="card-title">Chase Gate</h5>
                        <p class="card-text">Procesar tarjetas con Chase</p>
                        <a href="chase_gate.php" class="btn btn-primary btn-block">
                           <i class="fas fa-arrow-right"></i> Ir a Chase Gate
                        </a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
</main>

<style>
.card {
   transition: transform 0.3s ease, box-shadow 0.3s ease;
   border: 1px solid #333;
   background: rgba(45, 45, 45, 0.8);
}

.card:hover {
   transform: translateY(-5px);
   box-shadow: 0 10px 25px rgba(255, 215, 0, 0.3);
   border-color: #FFD700;
}

.btn-primary {
   background: linear-gradient(45deg, #FFD700, #FFA500);
   border: none;
   color: #000;
   font-weight: bold;
   transition: all 0.3s ease;
}

.btn-primary:hover {
   background: linear-gradient(45deg, #FFA500, #FFD700);
   transform: scale(1.05);
   box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
}

.card-title {
   color: #FFD700;
   font-weight: bold;
}

.card-text {
   color: #ccc;
}
</style>

</body>
</html>

