<div class='row-fluid clearfix'>
	<div class='small-12 medium-9 columns'>
		<h3><?php echo $application->title; ?></h3>
		<h4><small><?php echo $application->description; ?></small></h4>
	</div>
	<div class='small-12 medium-3 columns'>
		<div class='row'>
			<div class='small-6 columns'>
				<?php echo Html::box('/form-application/edit/' . $application->id, 'Edit', true, false, null, null, "isbox", null, "button expand"); ?>
			</div>
			<div class='small-6 columns'>
				<?php echo Html::b('/form-application/delete/' . $application->id, 'Delete', 'Are you sure you want to delete this application? All references to already entered data will be lost!', null, false, "warning expand"); ?>
			</div>
		</div>
	</div>
</div>