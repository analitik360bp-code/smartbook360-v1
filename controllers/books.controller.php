<?php

class BooksController
{

	public function manageBooks()
	{


		if (isset($_POST["id_table_book"])) {

			echo '<script>

				fncMatPreloader("on");
			    fncSweetAlert("loading", "Procesando...", "");

			</script>';

			$date_book = DateTime::createFromFormat('d/m/Y', $_POST["date_book"])->format('Y-m-d');

			/*=============================================
			Buscar si la reserva existe
			=============================================*/

			$url = "books?linkTo=id_table_book,date_book,time_book,confirm_book,id_office_book&equalTo=" . $_POST["id_table_book"] . "," . $date_book . "," . $_POST["time_book"] . ",1," . $_SESSION["admin"]->id_office_admin . "&select=id_book";
			$method = "GET";
			$fields = array();

			$getBook = CurlController::request($url, $method, $fields);

			if ($getBook->status != 200) {

				/*=============================================
				Creamos la reserva
				=============================================*/

				$num_book = TemplateController::genNums();

				$url = "books?token=" . $_SESSION["admin"]->token_admin . "&table=admins&suffix=admin";
				$method = "POST";
				$fields = array(
					"num_book" => $num_book,
					"id_table_book" => $_POST["id_table_book"],
					"date_book" => $date_book,
					"time_book" => $_POST["time_book"],
					"client_book" => trim($_POST["client_book"]),
					"email_book" => trim($_POST["email_book"]),
					"phone_book" => trim($_POST["phone_book"]),
					"servicios_book" => trim($_POST["servicios_book"]),
					"description_book" => trim($_POST["description_book"]),
					"confirm_book" => 0,
					"id_office_book" => $_SESSION["admin"]->id_office_admin,
					"date_created_book" => date("y-m-d")
				);

				$createBook = CurlController::request($url, $method, $fields);


				if ($createBook->status == 200) {

					/*=============================================
					Enviar confirmación por WhatsApp
					=============================================*/

					// Solo enviamos si hay número de teléfono
					if (!empty(trim($_POST["phone_book"]))) {

						// Consultar nombre del especialista
						$specialistName = '';
						$urlTable = "tables?linkTo=id_table&equalTo=" . $_POST["id_table_book"] . "&select=title_table";
						$getTable = CurlController::request($urlTable, "GET", []);

						if ($getTable->status == 200 && !empty($getTable->results)) {
							$specialistName = urldecode($getTable->results[0]->title_table);
						}

						$bookData = [
							'num' => $num_book,
							'client' => trim($_POST["client_book"]),
							'date' => $_POST["date_book"],
							'time' => $_POST["time_book"],
							'specialist' => $specialistName
						];
						
						require_once "controllers/whatsapp.controller.php";
						$wa = WhatsAppController::sendBookingConfirmation(
							$_POST["phone_book"],
							$bookData
						);
						
						// Log opcional — no bloqueamos el flujo si falla el WA
						if ($wa->status !== 200) {
							error_log("WhatsApp fallo [reserva #{$num_book}]: " . json_encode($wa->response));
						}
					} else {
						echo "<script>console.log('Error al enviar el WhatsApp - " . $num_book . "');</script>";
					}

					echo '
					<script>
						fncMatPreloader("off");
						fncFormatInputs();
						fncSweetAlert("success","La reserva ha sido creada con éxito",setTimeout(()=>location.reload(),1250));  
					</script>
					';
				}

				if($createBook->status == 200){

					$subject = "Confirmación de reserva";
					$email = $_POST["email_book"];
					$title = 'Confirmación de reserva';
					$message = '
					<div style="color:#555; font-size:16px; line-height:24px; padding: 0 20px;">
						<p>Hola <strong>'.$_POST["client_book"].'</strong>,</p>
						<p>Tu cita ha sido agendada con éxito. A continuación te compartimos los detalles de tu reserva:</p>

						<!-- Caja de detalles de la reserva -->
						<div style="background-color: #f9f9fb; border: 1px solid #eaeaea; border-radius: 6px; padding: 20px; margin: 25px 0;">
							
							<p style="margin: 0 0 10px 0;">
								<span style="font-size: 18px;">👨‍💼</span> <strong>Especialista:</strong><br>
								<span style="color: #333;">'.$specialistName.'</span>
							</p>

							<p style="margin: 0 0 10px 0;">
								<span style="font-size: 18px;">📅</span> <strong>Fecha y Hora:</strong><br>
								<span style="color: #333;">'.$_POST["date_book"].' '.$_POST["time_book"].'</span>
							</p>

						</div>

						<p style="text-align: center; margin-top: 30px;">Para que no lo olvides, puedes guardar esta cita en tu calendario:</p>
					</div>
					
					<h4 style="font-weight: 100; color:#999; padding:0px 20px">Ingrese para confirmar o cancelar su reserva</4>';
					$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
					$link = $scheme . "://" . $_SERVER["SERVER_NAME"]."/confirmacion?id=".$num_book;

					$sendEmail = TemplateController::sendEmail($subject, $email, $title, $message, $link, "SmartBook-Analitik360");

					if($sendEmail == "ok"){

						echo '<script>

								fncFormatInputs();
								fncMatPreloader("off");
								fncToastr("success", "Se ha enviado la confirmación de reserva por correo electrónico");

							</script>
						';

					}else{

						echo '<script>

							fncFormatInputs();
							fncMatPreloader("off");
							fncNotie("error", "'.$sendEmail.'");

							</script>
						';
					}
				}

			} else {

				echo '

				<script>

					fncMatPreloader("off");
					fncFormatInputs();
				    fncToastr("error","Error al reservar: La fecha y hora ya está reservada");	

				</script>

				';

			}

		}

	}

	public function getBookById()
	{
		if (isset($_POST["id"])) {

			$url_book = "books?token=no&except=num_book&id=".$_POST["id"]."&nameId=num_book";
			$method_book = "PUT";
			$fields_book = array(
				"confirm_book" => "1"
			);
			$fields_book = http_build_query($fields_book);
			$bookUpdate = CurlController::request($url_book, $method_book, $fields_book);

			if ($bookUpdate->status == 200) {

				echo '<script>
						fncMatPreloader("off");
						fncFormatInputs();
						fncSweetAlert("success","La confirmación de su reserva ha sido un éxito",setTimeout(()=>location.href = "confirmacion/confirm?id='.$_POST["id"].'",1250));  
					</script>';

			} else {
				echo '<script>onclick="window.location.href=\'/confirmacion/cancel\';"</script>';
			}
		}
	}

	public function cancelBook()
	{
		if (isset($_POST["id"])) {
			echo "<script>console.log('ID: " . $_POST["id"] . "');</script>";
			$url_cancel = "books?token=no&except=num_book&id=".$_POST["id"]."&nameId=num_book";
			$method_cancel = "PUT";
			$fields_cancel = array(
				"confirm_book" => "2",
				"id_motivo_book" => $_POST["id_cancel"]
			);
			$fields_cancel = http_build_query($fields_cancel);
			$bookUpdate = CurlController::request($url_cancel, $method_cancel, $fields_cancel);

			if ($bookUpdate->status == 200) {

				echo '<script>
						fncMatPreloader("off");
						fncFormatInputs();
						fncSweetAlert("success","La cancelación de su reserva ha sido un éxito",setTimeout(()=>location.href = "/",1250));  
					</script>';

			} else {
				echo '<script>onclick="window.location.href=\'/confirmacion/cancel\';"</script>';
			}
		}
	}

	public function confirmBook_Admin()
	{
		if (isset($_POST["id"])) {

			$url_cancel = "books?token=no&except=num_book&id=".$_POST["id"]."&nameId=num_book";
			$method_cancel = "PUT";
			if($_POST["confirmado"] == "1"){
				$fields_cancel = array(
					"confirm_book" => "1",
				);
			}else{
				$fields_cancel = array(
					"confirm_book" => "2",
					"id_motivo_book" => $_POST["motivo_cancelacion"]
				);
			}
			
			$fields_cancel = http_build_query($fields_cancel);
			$bookUpdate = CurlController::request($url_cancel, $method_cancel, $fields_cancel);

			if ($bookUpdate->status == 200) {

				echo '<script>
						fncMatPreloader("off");
						fncFormatInputs();
						fncSweetAlert("success","La cancelación de su reserva ha sido un éxito",setTimeout(()=>location.href = "",1250));  
					</script>';

			} else {
				echo '<script>onclick="window.location.href=\'/confirmacion/cancel\';"</script>';
			}
		}
	}

}