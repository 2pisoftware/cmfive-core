<style>
	p {
		margin-bottom: 0px;
	}
</style>
<div class='row-fluid clearfix panel'>
	<div class='small-12 medium-9 columns'>
		<p>Title: <?php echo $application->title; ?></p>
		<p>Description: <?php echo $application->description; ?></p>
		<p>Active: <?php echo $application->is_active == 1 ? 'Yes' : 'No'; ?></p>
	</div>
	<div class='small-12 medium-3 columns'>
		<div class='row'>
			<div class='small-6 columns'>
				<?php echo Html::b('/form-application/edit/' . $application->id, 'Edit', null, null, false, "button expand"); ?>
			</div>
			<div class='small-6 columns'>
				<?php echo Html::b('/form-application/delete/' . $application->id, 'Delete', 'Are you sure you want to delete this application? All references to already entered data will be lost!', null, false, "warning expand"); ?>
			</div>
		</div>
	</div>
</div>