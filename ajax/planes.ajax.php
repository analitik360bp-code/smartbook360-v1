<?php

require_once "../controllers/curl.controller.php";

class PlanesAjaxController {

    public $id_plan;

    public function getTimePlan(){
        /*=============================================
        Traer solo el time_plan por el id_plan
        =============================================*/
        $url = "planes?linkTo=id_plan&equalTo=".$this->id_plan."&select=time_plan";
        $method = "GET";
        $fields = array();

        $getPlan = CurlController::request($url, $method, $fields);
        
        if($getPlan->status == 200){

            $plan = $getPlan->results[0];

            // Retornamos la respuesta estructurada en JSON
            $response = array(
                "status" => 200,
                "time_plan" => $plan->time_plan
            );

            echo json_encode($response);

        } else {

            $response = array(
                "status" => 404,
                "message" => "Plan no encontrado o error en la API"
            );

            echo json_encode($response);
        }
    }
}

/*=============================================
Variables POST para activar AJAX
=============================================*/ 
if(isset($_POST["id_plan"])){

    $ajax = new PlanesAjaxController();
    $ajax -> id_plan = $_POST["id_plan"];
    $ajax -> getTimePlan(); 

}