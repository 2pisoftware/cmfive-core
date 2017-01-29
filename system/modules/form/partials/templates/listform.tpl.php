<div class='row-fluid'>
	<h4><?php echo $form->title; ?></h4>
	<?php echo Html::box("/form-instance/edit?form_id=" . $form->id . "&redirect_url=" . $redirect_url . "&object_class=" . get_class($object) . "&object_id=" . $object->id, "Add new " . $form->title, true); ?>
	<?php if (!empty($instances)) : ?>
		<table class='small-12'>
			<thead>
				<tr><?php echo $form->getTableHeaders(); ?><td>Actions</td></tr>
			</thead>
			<tbody>
				<?php foreach($instances as $instance) : ?>
					<tr>
						<?php echo $instance->getTableRow(); ?>
						<td>
							<?php echo Html::box("/form-instance/edit/" . $instance->id . "?form_id=" . $form->id . "&redirect_url=" . $redirect_url . "&object_class=" . get_class($object) . "&object_id=" . $object->id, "Edit", true); ?>
							<?php echo Html::b("/form-instance/delete/" . $instance->id . "?redirect_url=" . $redirect_url, "Delete", "Are you sure you want to delete this item?"); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php echo $form->getSummaryRow($object); ?>
			</tbody>
		</table>
	<?php else : ?>
		<p>No <?php echo $form->title; ?> form data found</p>
	<?php endif; ?>
</div>