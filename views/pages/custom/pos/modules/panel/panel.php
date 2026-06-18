<div class="order-summary">
  <div class="order-summary-header">
    <h4 class="mb-1" id="transactionOrder" idOrder="<?php echo isset($idOrder) ? $idOrder : "" ?>" idTable="<?php echo isset($idTable) ? $idTable : "" ?>" processOrder="<?php echo isset($processOrder) ? $processOrder : "" ?>">Orden # <?php echo isset($transactionOrder) ? $transactionOrder : "" ?></h4>
    <p class="text-center p-0"><?php echo isset($dateOrder) ? TemplateController::formatDate(4,$dateOrder) : "" ?></p>
  </div>

  <div class="small text-center bg-gray rounded px-3 py-1 mb-3"> <?php echo explode("@",$_SESSION["admin"]->email_admin)[0]  ?> </div>

  <?php if (isset($processOrder) && $processOrder != "Entregada"): ?>
  
    <div class="order-items" id="order-items">

      <?php if (!empty($sales)): ?>

        <?php foreach ($sales as $key => $value): ?>

          <?php if ($value->process_sale == "Preparando"): ?>

            <div class="order-item" 
            data-id="<?php echo $value->id_food_sale ?>"
            data-name="<?php echo urldecode($value->title_food) ?>"
            data-qty="<?php echo $value->qty_sale ?>"
            data-price="<?php echo $value->subtotal_sale ?>" >
              <div class="order-item-header">
                <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>
                <div class="order-item-controls">
                  <button class="quantity-btn decrease-qty">
                    <i class="bi bi-dash"></i>
                  </button>
                  <span class="quantity-display">x<?php echo $value->qty_sale ?></span>
                  <button class="quantity-btn increase-qty">
                    <i class="bi bi-plus"></i>
                  </button>
                  <button class="quantity-btn ms-2 remove-item" style="background:#dc3545; border-color:#dc3545; color:white">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
              <div class="d-flex justify-content-between">
                <span class="order-item-price">$<?php echo number_format($value->subtotal_sale,2) ?></span>
              </div>
            </div>
          <?php endif ?>

        <?php endforeach ?>

      <?php else: ?>

        <div class="empty-order">
          <i class="bi bi-cart3"></i>
          <p>No hay items añadidos</p>
        </div>
        
      <?php endif ?>
      
    </div>

    <div class="order-notes">
      <textarea class="form-control" id="note_order" placeholder="Adicionar notas a esta orden..." rows="3"><?php echo isset($noteOrder) ? ($noteOrder ? $noteOrder : null) : null ?></textarea>
    </div>

    <div class="order-actions">
      <button class="btn btn-success btn-lg w-100 mb-2" id="submit-order" idOrder="<?php echo isset($idOrder) ? $idOrder : "" ?>">
        <i class="bi bi-check-circle"></i> Enviar Orden
      </button>

      <?php if (isset($processOrder) && $processOrder == "Ordenando"): ?>
        <button class="btn btn-dark w-100" id="clear-order">
          <i class="bi bi-trash"></i> Eliminar todos los items
        </button>
      <?php endif ?>
    </div>
  <?php endif ?>

  <div class="order-items" id="order-items">
    
    <?php if (!empty($sales)): ?>

      <?php foreach ($sales as $key => $value): ?>

        <?php if ($value->process_sale == "Entregada"): ?>

          <div class="order-item-finish" 
          data-id="<?php echo $value->id_food_sale ?>"
          data-name="<?php echo urldecode($value->title_food) ?>"
          data-qty="<?php echo $value->qty_sale ?>"
          data-price="<?php echo $value->subtotal_sale ?>">

            <div class="order-item-header">
             
              <span class="order-item-name"><?php echo urldecode($value->title_food) ?></span>

              <div class="order-item-controls">
                
                <span class="quantity-display">x<?php echo $value->qty_sale ?></span>

                <button class="ms-2 bg-info border-0 p-0 rounded  px-1">
                    <i class="fa-solid fa-check"></i>
                </button>

              </div>

            </div>

            <div class="d-flex justify-content-between">
              <span class="order-item-price">$<?php echo number_format($value->subtotal_sale,2) ?></span>
            </div>

          </div>

        <?php endif ?>

      <?php endforeach ?>
    <?php endif ?>
  </div>

  <div class="order-total">
    <div class="d-flex justify-content-between">
      <span>Subtotal:</span>
      <span id="subtotal">$<span id="subtotalValue">0.00</span></span>
    </div>
    <div class="d-flex justify-content-between">
      <!--<span>Tax (<?php //echo isset($tax) ? $tax*100 : 0 ?>%):</span>
      <span id="tax">$<span id="taxValue">0.00</span></span>-->
    </div>
    <hr>
    <div class="d-flex justify-content-between total-line">
      <strong>Total:</strong>
      <strong id="total">$<span id="totalValue">0.00</span></strong>
    </div>
  </div>

  <?php if (isset($processOrder) && $processOrder != "Entregada"): ?>

   <div class="order-payment mt-3">
    
    <button class="btn backColor btn-lg w-100 mb-2" data-bs-toggle="modal" data-bs-target="#myCheckout">
      <i class="fa-solid fa-cash-register"></i> Pagar Orden    
    </button>

  </div>
    
  <?php endif ?>

 

</div>