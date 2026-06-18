$(function(){

  let orderItems = {};
  let orderTotal = 0;
  let processOrder = $("#transactionOrder").attr("processOrder");
  let goContinue = false;

  if(processOrder != "Entregada"){

    goContinue = true;
  
  }

  /*=============================================
  Cargar items de venta
  =============================================*/

  if($(".order-item").length > 0){

    const order_item = $(".order-item");

    order_item.each((i)=>{

      orderItems[$(order_item[i]).data("id")] = {
          
          id:$(order_item[i]).data("id"),
          order:$("#transactionOrder").attr("idOrder"),
          name: $(order_item[i]).data("name"),
          price: Number($(order_item[i]).data("price"))/Number($(order_item[i]).data("qty")),
          quantity: $(order_item[i]).data("qty")
      }

    })

  }

  updateTotals();

  /*=============================================
  Capturar notas
  =============================================*/

  $(document).on("change","#note_order",function(){

      updateOrderDisplay();
      fncToastr("success", "Notas Modificadas");
  })

  /*=============================================
  Tabular categorías
  =============================================*/
  $(document).on('click', '.category-tab', function() {

    $('.category-tab').removeClass('active');
    $('.menu-category').removeClass('active');

    $(this).addClass('active');

    const category = $(this).data('category');

    $('#' + category).addClass('active');

  })

  /*=============================================
   Click para agregar item del menú
  =============================================*/

  if(goContinue){

    $(document).on("click", ".menu-item", function () {

      const itemId = $(this).data("item");
      const itemName = $(this).find('.menu-item-name').text();
      const itemPrice = parseFloat($(this).data('price'));

      addToOrder(itemId, itemName, itemPrice);

    })

  }



  /*=============================================
   Función para agregar item a la orden
  =============================================*/

  function addToOrder(itemId, itemName, itemPrice) {

    if(!goContinue){
      return;
    }
    
    if(orderItems[itemId]) {
     
      orderItems[itemId].quantity += 1;

    }else{

      orderItems[itemId] = {
          name: itemName,
          price: itemPrice,
          quantity: 1
      };

    }

    updateOrderDisplay();
    fncToastr("success", `${itemName} adicionad@ a la orden`);
  
  }

  /*=============================================
  Función para renderizar el pedido
  =============================================*/
  function updateOrderDisplay() {

    let $container = $('#order-items');
    let arrayItems = [];
    let html = '';

    if (Object.keys(orderItems).length === 0) {

      html += `
        <div class="empty-order">
        <i class="bi bi-cart3"></i>
        <p>No hay items añadidos</p>
        </div>
      `;

      updateTotals();

      $("#note_order").val("");

      arrayItems = [{"order":0, "note":""}];
      arrayItems[0].order = $("#transactionOrder").attr("idOrder");
      arrayItems[0].note = $("#note_order").val();

    }

    

    $.each(orderItems, function(itemId, item) {

      html += `
        <div class="order-item" data-id="${itemId}">
          <div class="order-item-header">
            <span class="order-item-name">${item.name}</span>
            <div class="order-item-controls">
              <button class="quantity-btn decrease-qty">
                <i class="bi bi-dash"></i>
              </button>
              <span class="quantity-display">x${item.quantity}</span>
              <button class="quantity-btn increase-qty">
                <i class="bi bi-plus"></i>
              </button>
              <button class="quantity-btn ms-2 remove-item" style="background:#dc3545; border-color:#dc3545; color:white">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
          <div class="d-flex justify-content-between">
            <span class="order-item-price">$${(item.price * item.quantity).toFixed(2)}</span>
          </div>
        </div>
      `;

      item.id = itemId;
      item.order = $("#transactionOrder").attr("idOrder");
      item.note = $("#note_order").val();

      arrayItems.push(item);
     
    })

    $container.html(html);

    updateTotals();

    /*=============================================
    Actualizar items de ventas
    =============================================*/

    var data = new FormData();
    data.append("items_sale",JSON.stringify(arrayItems));
    data.append("id_admin",$("#idAdmin").val());
    data.append("id_office",$("#idOffice").val());
    data.append("token", localStorage.getItem("tokenAdmin"));

     $.ajax({
        url:"/ajax/pos.ajax.php",
        method: "POST",
        data: data,
        contentType: false,
        cache: false,
        processData: false,
        success: function (response){
          
           console.log("response", response);
        
        }

      })


  }

 /*=============================================
  Actualizar totales
  =============================================*/

  function updateTotals() {
    
    let subtotal = 0;
    
    $.each(orderItems, function(_id, item) {
      subtotal += item.price * item.quantity;
    });

    let orderItemFinish = $(".order-item-finish");

    if(orderItemFinish.length > 0){

      orderItemFinish.each((i)=>{

        subtotal += Number($(orderItemFinish[i]).attr("data-price"));
       
      })

    }

    const tax = subtotal * Number($("#taxSystem").val());
    const total = subtotal;

    $('#subtotal').html(`$<span id="subtotalValue">${subtotal.toFixed(2)}</span>`);
    $('#tax').html(`$<span id="taxValue">${tax.toFixed(2)}</span>`);
    $('#total').html(`$<span id="totalValue">${total.toFixed(2)}</span>`);

    orderTotal = total;
  }

  /*=============================================
  Incrementar cantidad
  =============================================*/

  $(document).on("click", ".increase-qty", function (e) {

    e.preventDefault();

    const itemId = $(this).closest(".order-item").data("id");

    if (orderItems[itemId]) {

      orderItems[itemId].quantity += 1;
      updateOrderDisplay()
    
    }

  })

  /*=============================================
  Disminuir cantidad
  =============================================*/

  $(document).on('click', '.decrease-qty', function(e) {

    e.preventDefault();

    const itemId = $(this).closest('.order-item').data('id');

    if (!orderItems[itemId]) return;

    if (orderItems[itemId].quantity > 1) {

      orderItems[itemId].quantity -= 1;
      updateOrderDisplay();

    }else{

      fncSweetAlert("confirm", "¿Está seguro de remover este item?", "").then(resp=>{

        if(resp){

          delete orderItems[itemId];
          updateOrderDisplay();
        
        }

      })
    }

  })

  /*=============================================
  Remover Item
  =============================================*/

  $(document).on("click", ".remove-item", function (e) {

    e.preventDefault();

    fncSweetAlert("confirm", "¿Está seguro de remover este item?", "").then(resp=>{

      if(resp){


        const itemId = $(this).closest(".order-item").data("id");

        if (orderItems[itemId]) {
            
          delete orderItems[itemId];
          
          updateOrderDisplay();

          fncToastr("success", "Item removido de la orden");

        }
      
      }

    })

  })

  /*=============================================
  Limpiar el pedido
  =============================================*/
  $('#clear-order').on('click', function() {
    
    if (Object.keys(orderItems).length === 0) return;

    fncSweetAlert("confirm", "¿Borrar todos los items de este pedido?", "").then(resp=>{

      if(resp){

        clearOrder();

      }

    })
  
  });

  /*=============================================
  Función que limpia el pedido
  =============================================*/

  function clearOrder() {
    
    orderItems = {};
    updateOrderDisplay();
    $('#note_order').val('');
    fncToastr("success", "Orden limpiada");
  }

  /*=============================================
   Enviar pedido a cocina
  =============================================*/

  $("#submit-order").on("click", function () {

    if (Object.keys(orderItems).length === 0) {
      fncToastr("error", "Por favor adiciona items a la orden");
      return;
    }

    fncSweetAlert("confirm", "¿Enviar esta orden a En Servicio?", "").then(resp=>{

      if(resp){

        /*=============================================
        Actualizar proceso de la orden
        =============================================*/
        var data = new FormData();

        data.append("id_order",$(this).attr("idOrder"));
        data.append("process_order","Preparando");
        data.append("token", localStorage.getItem("tokenAdmin"));

        $.ajax({
          url:"/ajax/pos.ajax.php",
          method: "POST",
          data: data,
          contentType: false,
          cache: false,
          processData: false,
          success: function (response){

            if(response == 200){

              fncToastr("success", "Orden enviada a En Servicio");

            }
          
          }

        })

      }

    })


  })

  /*=============================================
  Cambiar estado del pedido
  =============================================*/

  $(document).on("click",".changeProcessItem",function(){

    /*=============================================
    Actualizar proceso de los items
    =============================================*/

    var elem = $(this);

    var data = new FormData();
    data.append("id_sale",$(this).attr("idSale"));
    data.append("process_sale","Entregada");
    data.append("id_office",$("#idOffice").val());
    data.append("token", localStorage.getItem("tokenAdmin"));

    $.ajax({
      url:"/ajax/pos.ajax.php",
      method: "POST",
      data: data,
      contentType: false,
      cache: false,
      processData: false,
      success: function (response){

        if(response == 200){

          $(elem).removeClass("bg-light");
          $(elem).addClass("bg-info");
          $(elem).html(`<i class="fa-solid fa-check"></i>`);

          fncToastr("success", "Item listo para entregar");
        
        }else{

           fncToastr("error", ` "${response}" para ser preparado`);
        }

      }

    })


  })

  /*======================================
  =            Eliminar Orden            =
  ======================================*/

  $(document).on("click",".deleteOrder",function(){
    
    if($(this).attr("processOrder") != "Ordenando"){
      
      fncToastr("error", `Esta orden no se puede eliminar`);
      
      return;
      
    }
    
    fncSweetAlert("confirm", "¿Está seguro de eliminar esta orden?", "").then(resp=>{
      
      if(resp){
        
        var data = new FormData();
        data.append("id_order_delete",$(this).attr("idOrder"));
        data.append("id_table_delete",$(this).attr("idTable"));
        data.append("token", localStorage.getItem("tokenAdmin"));
        
        $.ajax({
          url:"/ajax/pos.ajax.php",
          method: "POST",
          data: data,
          contentType: false,
          cache: false,
          processData: false,
          success: function (response){
            console.log(response);
            
            if(response == 200){

              fncSweetAlert("success", "Orden eliminada con éxito", setTimeout(function(){ window.location = "/" },1250));
              console.log("Orden eliminada con éxito");
            }

          }

        })

      }

    })

  })
    
});