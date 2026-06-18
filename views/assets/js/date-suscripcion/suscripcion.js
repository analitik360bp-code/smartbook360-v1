$(document).on("change",".changeDate", function(){

	fncSweetAlert("loading", "Costeando producto...", "");

	var id_plan = $("#id_plan_suscripcion").val();
	//var fecha_ini = $("#start_date_suscripcion").val();

	var data = new FormData();
	data.append("id_plan",id_plan);

	$.ajax({
		url:"/ajax/planes.ajax.php",
		method: "POST",
		data: data,
		contentType: false,
		cache: false,
		processData: false,
		success: function (response){

			fncSweetAlert("close", "", "");
			
			if(response == "error"){

				fncToastr("error","Existe un producto sin compra o sin costo unitario");

				return;

			}else{

                // 1. Parsear la respuesta para obtener el número de meses (ej: 6)
                var data = JSON.parse(response);
                var mesesASumar = parseInt(data.time_plan, 10); // Aseguramos que sea un entero

                // 2. Obtener el valor de la fecha de inicio (formato YYYY-MM-DD)
                var fecha_ini = $("#start_date_suscripcion").val();

                if (fecha_ini && !isNaN(mesesASumar)) {
                    
                    // 3. Separar la fecha para evitar desfases de zona horaria en JS
                    var partes = fecha_ini.split('-');
                    var año = parseInt(partes[0], 10);
                    var mes = parseInt(partes[1], 10) - 1; // Los meses en JS van de 0 a 11
                    var dia = parseInt(partes[2], 10);

                    // 4. Crear el objeto de fecha base
                    var fechaFin = new Date(año, mes, dia);

                    // 5. Sumar los meses enteros obtenidos de la respuesta
                    fechaFin.setMonth(fechaFin.getMonth() + mesesASumar);

                    // 6. Formatear la nueva fecha a "YYYY-MM-DD"
                    var yyyy = fechaFin.getFullYear();
                    var mm = String(fechaFin.getMonth() + 1).padStart(2, '0'); // Volver a formato 1-12
                    var dd = String(fechaFin.getDate()).padStart(2, '0');

                    var fechaFinFormateada = yyyy + '-' + mm + '-' + dd;

                    // 7. Asignar el resultado final al valor del input de fecha fin
                    $("#end_date_suscripcion").val(fechaFinFormateada);
                    
                    // Opcional: Si quieres actualizar también la clase changeDate que mencionaste antes
                    //$(".changeDate").val(fechaFinFormateada);
                   // $("#end_date_suscripcion").val(fechaFinFormateada);

                } else {
                    console.warn("No se pudo calcular la fecha fin. Verifica que la fecha de inicio esté seleccionada.");
                }
                console.log(JSON.parse(response).time_plan);
			}

		}

	})
})
