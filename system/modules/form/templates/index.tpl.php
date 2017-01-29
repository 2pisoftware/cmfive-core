<div class="row-fluid">
	<div class="small-12">
		<?php echo Html::box("/form/edit", "Add a form", true); ?>
	</div>
</div>

<?php if (!empty($forms)) : ?>
<table class="table small-12">
	<thead>
		<tr><th>Title</th><th>Description</th><th>Actions</th></tr>
	</thead>
	<tbody>
		<?php foreach($forms as $form) : ?>
			<tr>
				<td width="30%"><?php echo $form->toLink(); ?></td>
				<td width="50%"><?php echo $form->description; ?></td>
				<td width="20%">
					<?php echo Html::box("/form/edit/" . $form->id, "Edit", true) ?>
					<?php echo Html::b("/form/delete/" . $form->id, "Delete", "Are you sure you want to delete this form? (WARNING: there may be existing data saved to this form!)", null, false, "alert"); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
