<?php echo Html::box('/form-application/edit', 'Create Application', true); ?>

<?php if (!empty($application_table_data)) : ?>
	<?php echo Html::table($application_table_data, null, 'tablesorter', !empty($application_table_header) ? $application_table_header : null); ?>
<?php else : ?>
	<h3>No Applications found</h3>
<?php endif;