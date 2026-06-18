<div class="container-fluid backgroundImage" <?php if (!empty($admin->back_admin)): ?>
	style="background-image: url(<?php echo $admin->back_admin ?>)"
<?php endif ?>>

	
	<div class="d-flex flex-wrap justify-content-center align-content-center vh-100">

		<div class="card rounded p-4 w-25 text-center" style="min-width: 320px !important;">

			<h1 class="textColor">BIENVENIDO</h1>

			
			<h3><?php echo $admin->symbol_admin ?> <?php echo $admin->title_admin ?></h3>

			<p>
			Sistema POS Multisucursal para Restaurantes
			<a href="/"><strong>Navega a la página de inicio</strong></a>.
			<p>

		</div>

	</div>

</div>