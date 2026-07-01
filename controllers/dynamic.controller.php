<?php 

class DynamicController{

	/*=============================================
	Gestión de datos dinámicos
	=============================================*/	

	public function manage(){

		if(isset($_POST["module"])){

			echo '<script>

				fncMatPreloader("on");
			    fncSweetAlert("loading", "Procesando...", "");

			</script>';

			$module = json_decode($_POST["module"]);

			/*=============================================
			Editar datos
			=============================================*/

			if(isset($_POST["idItem"])){

				/*=============================================
				Actualizar datos
				=============================================*/

				$url = $module->title_module."?id=".base64_decode($_POST["idItem"])."&nameId=id_".$module->suffix_module."&token=".$_SESSION["admin"]->token_admin."&table=admins&suffix=admin";
				$method = "PUT";
				$fields = "";
				$count = 0;

				foreach ($module->columns as $key => $value) {

					if($value->type_column == "password" && !empty($_POST[$value->title_column])){

						$fields.= $value->title_column."=".crypt(trim($_POST[$value->title_column]),'$2a$07$azybxcags23425sdg23sdfhsd$')."&";

					}else if($value->type_column == "email"){

						$fields.= $value->title_column."=".trim($_POST[$value->title_column])."&";

					}else{
					
						if(isset($_POST[$value->title_column])){

							$fields.= $value->title_column."=".urlencode(trim($_POST[$value->title_column]))."&";

						}

					}
					
					$count++;

					if($count == count($module->columns)){

						$fields = substr($fields,0,-1);

						$update = CurlController::request($url,$method,$fields);

						if($update->status == 200){

							echo '

								<script>

									fncMatPreloader("off");
									fncFormatInputs();
								    fncSweetAlert("success","El registro ha sido actualizado con éxito", setTimeout(()=>window.location="/'.$module->url_page.'",1000));
									

								</script>

							';
							
						}else{
							
							if($update->status == 303){

								echo '

									<script>

										fncMatPreloader("off");
										fncFormatInputs();
									    fncSweetAlert("error","El token ha expirado, inicia sesión nuevamente", setTimeout(()=>window.location="/logout",1000));
										

									</script>

								';

							}
						}
					}
				
				}


			}else{
		
				/*=============================================
				Crear datos
				=============================================*/

				$url = $module->title_module."?token=".$_SESSION["admin"]->token_admin."&table=admins&suffix=admin";
				$method = "POST";
				$fields = array();
				$count = 0;
				
				foreach ($module->columns as $key => $value) {

					if($value->type_column == "password"){

						if(isset($_POST[$value->title_column])){

							$fields[$value->title_column] = crypt(trim($_POST[$value->title_column]),'$2a$07$azybxcags23425sdg23sdfhsd$');

						}
					
					}else if($value->type_column == "email"){

						$fields[$value->title_column] = trim($_POST[$value->title_column]);
					}else{
						//&& $_POST[$value->title_column] != $_POST["servicio_table"]
						if(isset($_POST[$value->title_column]) && $_POST[$value->title_column] != $_POST["salida_table"] && $_POST[$value->title_column] != $_POST["entrada_table"] && $_POST[$value->title_column] != $_POST["servicio_table"]){

							$fields[$value->title_column] = urlencode(trim($_POST[$value->title_column]));

						}else{
							$fields["servicio_table"] = $_POST["servicio_table"];
							$fields["salida_table"] = $_POST["salida_table"];
							$fields["entrada_table"] = $_POST["entrada_table"];
						}

					}
					
					$count++;

					if($count == count($module->columns)){

						$fields["date_created_".$module->suffix_module] = date("Y-m-d");

						$save = CurlController::request($url,$method,$fields);

						echo "<script>console.log('" . $url . "');</script>";
						echo '<script>console.log("' .print_r($fields, true). '");</script>';
						if($save->status == 200){

							echo '

								<script>

									fncMatPreloader("off");
									fncFormatInputs();
								    fncSweetAlert("success","El registro ha sido guardado con éxito", setTimeout(()=>window.location="/'.$module->url_page.'",1000));
									

								</script>

							';
							
						}else{

							if($save->status == 303){

								echo '

									<script>

										fncMatPreloader("off");
										fncFormatInputs();
									    fncSweetAlert("error","El token ha expirado, inicia sesión nuevamente", setTimeout(()=>window.location="/logout",1000));
										

									</script>

								';

							}


						}
					}
				
				}

			}

		}

	}

}