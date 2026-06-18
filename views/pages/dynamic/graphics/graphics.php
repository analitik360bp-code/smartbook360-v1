<?php 

$xAxis = array();
$yAxis = array();


$content = json_decode($module->content_module);
$idOffice = $_SESSION["admin"]->id_office_admin;

/*=============================================
Leer fechas del filtro
=============================================*/

$dateFrom   = $_GET['date_from'] ?? date('Y-m-01');
$dateTo     = $_GET['date_to']   ?? date('Y-m-d');
$dateColumn = 'date_created_'.substr($content->table, 0, -1); // ajusta si es necesario

/*=============================================
Leer Datos de base
=============================================*/
//print_r($content);
if($content->relation == ""){
	$url = $content->table
			."?linkTo=".$dateColumn
			."&between1=".$dateFrom
			."&between2=".$dateTo
			."&select=".$content->xAxis.",".$content->yAxis
			."&filterTo=id_office_".substr($content->table, 0, -1)
			."&inTo=".$idOffice;

}else{

	$url = "relations?rel=".$content->table.",".$content->relation."&type=".substr($content->table, 0, -1).",".substr($content->relation, 0, -1)
			."&linkTo=".$dateColumn
			."&between1=".$dateFrom
			."&between2=".$dateTo
			."&select=".$content->xAxis.",".$content->yAxis
			."&filterTo=id_office_".substr($content->table, 0, -1)
			."&inTo=".$idOffice;
		
	//echo print_r($url);
}
	$method = "GET";
	$fields = array();

	$response = CurlController::request($url,$method,$fields);

//echo print_r(json_encode($response));
if($response->status == 200){

	$graphic = $response->results;

	foreach (json_decode(json_encode($graphic),true) as $index => $item) {

		array_push($xAxis, $item[$content->xAxis]);
		$yAxis[$item[$content->xAxis]] = 0;
		//echo print_r($item[$content->xAxis]);
		
	}

	$xAxis = array_values(array_unique($xAxis));

	foreach (json_decode(json_encode($graphic),true) as $index => $item) {
		
		for($i = 0; $i < count($xAxis); $i++){

			if($xAxis[$i] == $item[$content->xAxis]){

				$yAxis[$item[$content->xAxis]]++;
				//$yAxis[$item[$content->xAxis]] +=  $item[$content->yAxis];
				
			}
		}

	}
}

?>

<div class="<?php if ($module->width_module == "100"): ?> col-lg-12 <?php endif ?><?php if ($module->width_module == "75"): ?> col-lg-9 <?php endif ?><?php if ($module->width_module == "50"): ?> col-lg-6 <?php endif ?><?php if ($module->width_module == "33"): ?> col-lg-4 <?php endif ?><?php if ($module->width_module == "25"): ?> col-lg-3 <?php endif ?> col-12 mb-3 position-relative">

	<?php if ($_SESSION["admin"]->rol_admin == "superadmin"): ?>

		<div class="position-absolute border rounded bg-white" style="top:0px; right:12px; z-index:100">
			
			<button type="button" class="btn btn-sm text-muted rounded m-0 px-1 py-0 border-0 myModule" item='<?php echo json_encode($module) ?>' idPage="<?php echo $page->results[0]->id_page ?>">
				<i class="bi bi-pencil-square"></i>
			</button>

			<button type="button" class="btn btn-sm text-muted rounded m-0 px-1 py-0 border-0 deleteModule" idModule=<?php echo base64_encode($module->id_module) ?> >
				<i class="bi bi-trash"></i>
			</button>


		</div>
		
	<?php endif ?>

	
	<div class="card rounded">
		
		<div class="card-header bg-white rounded-top h4 font-weight-bold text-capitalize py-3">
			<?php echo $module->title_module ?>
		</div>

		<div class="card-body p-4">
			<canvas id="chart-<?php echo str_replace(" ","_",$module->title_module) ?>" height="500"></canvas>
		</div>

	</div>

</div>

<script>
	
if($("#chart-<?php echo str_replace(" ","_",$module->title_module) ?>").length > 0){

	var graphicChart = $("#chart-<?php echo str_replace(" ","_",$module->title_module) ?>");
	var tagsChart = new Chart(graphicChart, {

		type: "<?php echo $content->type ?>",
		data: {
			labels:[

				<?php 

					foreach ($xAxis as $index => $item){

						echo "'".$item."',";

					}

				?>

				],
			datasets:[
				{
					backgroundColor: 'rgba(<?php echo $content->color ?>,.55)',
					borderColor: 'rgb(<?php echo $content->color ?>)',
					data: [

						<?php 

							foreach ($xAxis as $index => $item){

								echo "'".$yAxis[$item]."',";

							}

						?>


					]
				}
			]
		},//close data
		options: {
	        maintainAspectRatio: false,
	        tooltips: {
	          mode: 'index',
	          intersect: true
	        },
	        hover: {
	          mode: 'index',
	          intersect: true
	        },
	        legend: {
	          display: false
	        },
	        scales: {
	        	yAxes: [{
        		 	display: true,
		            gridLines: {
		              display: true
		            },
		            ticks: $.extend({
         			  beginAtZero: true,
		              // Include a dollar sign in the ticks
		              callback: function (value) {
		                if (value >= 1000) {
		                  value /= 1000
		                  value += 'k'
		                }

		                return  value
		              }
		            }, 
		            {
	                  fontColor: '#495057',
	                  fontStyle: 'bold'
            		})

            	}],
            	xAxes: [{
		            display: true,
		            gridLines: {
		              display: true
		            },
		            ticks: {
	                  fontColor: '#495057',
	                  fontStyle: 'bold'
	                }
	          	}]

	        }//close scales

	    }//close options

	})
}

</script>