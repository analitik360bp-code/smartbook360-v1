$(document).ready(function () {
	
    /*=============================================
	Elegir el método de pago
	=============================================*/
	
    const $paymentOptions = $(".payment-option");
    const $selectedPaymentMethod = $("#selectedPaymentMethod");

    $paymentOptions.on("click", function () {

    	$paymentOptions.removeClass("active");
    	$(this).addClass("active");

    	const method = $(this).data("method");
    	$selectedPaymentMethod.val(method);

    })

    /*=============================================
	Cargar valores de propina y totales
	=============================================*/
	
    setTimeout(function(){

    	let subtotal = $("#subtotalValue").html();
    	let total = $("#totalValue").html();
    	let tip = Number(subtotal)*Number($("#tipSystem").val());

    	$("#paymentTip").val(tip.toFixed(2))
    	$("#paymentAmount").val((Number(total)+tip).toFixed(2));

    },1000)


    $(document).on("change","#paymentTip", function(){

        let total = $("#totalValue").html();
        let tip = parseFloat($(this).val());

        $("#paymentAmount").val((Number(total)+tip).toFixed(2));

	
    })

    /*=============================================
	Click a procesar el pago
	=============================================*/

	$(document).on("click", "#processPaymentBtn", function(){

		const total = parseFloat($("#paymentAmount").val());   

		if (total <= 0 || isNaN(total)) {
            fncToastr("error", "Valida el total de la orden");
            return;
        }  

        const $btn = $(this);

        $btn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
        $btn.prop("disabled", true);

        var data = new FormData();
        data.append("subtotal",parseFloat($("#subtotalValue").html()));
        data.append("tax",parseFloat($("#taxValue").html()));
        data.append("total",parseFloat($("#totalValue").html()));
        data.append("tip",parseFloat($("#paymentTip").val()));
        data.append("idOrder",$("#transactionOrder").attr("idOrder"));
        data.append("idTable",$("#transactionOrder").attr("idTable"));
        data.append("method", $("#selectedPaymentMethod").val());
        data.append("token", localStorage.getItem("tokenAdmin"));

        $.ajax({
            url:"/ajax/checkout.ajax.php",
            method: "POST",
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            success: function (response){
                
                if(response == 200){
                    
                    $btn.html("Procesar el Pago");
                    $btn.prop("disabled", false);
                    const modalEl = document.getElementById("myCheckout");
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Show success message
                    fncSweetAlert("success",
                        `Pago de $${total.toFixed(2)} procesado correctamente via ${$("#selectedPaymentMethod").val().toUpperCase()}!`,setTimeout(()=>window.location="/",1250)
                    );
                    
                  
            	}else{
            		fncToastr("error","Error al procesar el pago");  

            	}

            }

        })

	})

});