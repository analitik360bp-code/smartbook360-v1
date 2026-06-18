<?php if ($module->columns[$i]->type_column == "date"): ?>

	<div class="input-group">
		
		<input 
		type="text" 
		class="form-control rounded-start datepicker
		<?php if ($module->columns[$i]->title_column == "start_date_suscripcion"): ?> changeDate <?php endif ?>
		" 
		<?php if ($module->columns[$i]->title_column == "end_date_suscripcion"): ?>readonly <?php endif ?>
		placeholder="YYYY-mm-dd"
		id="<?php echo $module->columns[$i]->title_column ?>"  
		name="<?php echo $module->columns[$i]->title_column ?>"
		value="<?php if (!empty($data)): ?><?php echo urldecode($data[$module->columns[$i]->title_column]) ?><?php endif ?>"
		>

		<div class="input-group-text rounded-end">
			<i class="bi bi-calendar"></i>
		</div>

	</div>

<?php endif ?>