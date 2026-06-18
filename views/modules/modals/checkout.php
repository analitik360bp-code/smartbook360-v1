<link rel="stylesheet" href="/views/assets/css/checkout/checkout.css">

<div class="modal fade" id="myCheckout">
	
	<div class="modal-dialog modal-dialog-centered">

		<div class="modal-content rounded">
			
			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"><?php echo isset($titleTable) ? urldecode($titleTable) : "Cuenta" ?></h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body checkout-modal-body">

				<!-- Payment Method Section -->
				<div class="payment-section mb-4">
					
					<h5 class="payment-title mb-3">Método de Pago</h5>

					<div class="payment-methods d-flex gap-3 mb-4">
						
						<div class="payment-option active" data-method="cash">
							
							<i class="fas fa-money-bill-wave"></i>
							<span>Efectivo</span>

						</div>

						<div class="payment-option" data-method="card">
							
							<i class="fas fa-credit-card"></i>
							<span>Tarjeta</span>

						</div>

						<div class="payment-option" data-method="transfer">
							<i class="fas fa-university"></i>
							<span>Transferencia</span>
						</div>

					</div>


				</div>

				<!--Payment Tip -->
				<div class="amount-section mb-4">
					<h5 class="payment-title mb-3">Propina</h5>
					<div class="amount-input-container">
						<span class="currency-symbol">$</span>
						<input type="number" class="form-control amount-input" value="0.0" step="0.01" min="0" id="paymentTip">
					</div>
				</div>

				<!-- Amount Section -->
				<div class="amount-section mb-4">
					<h5 class="payment-title mb-3">Total</h5>
					<div class="amount-input-container">
						<span class="currency-symbol">$</span>
						<input type="number" class="form-control amount-input" value="0" step="0.01" min="0" id="paymentAmount">
					</div>
				</div>

				<input type="hidden" id="selectedPaymentMethod" name="payment_method" value="cash">

				<!-- Process Payment Button -->
				<div class="payment-action">
					<button type="button" class="btn btn-success process-payment-btn w-100 backColor" id="processPaymentBtn">
						Procesar el Pago
					</button>
				</div>

			</div>

			<!-- Modal footer -->
			<div class="modal-footer d-flex justify-content-between">
				<div><button type="button" class="btn btn-dark rounded" data-bs-dismiss="modal">Cerrar</button></div>
			</div>

		</div>
		
	</div>

</div>

<script src="/views/assets/js/checkout/checkout.js"></script>