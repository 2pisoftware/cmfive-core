<?php echo Html::b('/form-application/edit', 'Create Application');

if (!empty($application_table_data)) :
	echo Html::table($application_table_data, null, 'tablesorter', !empty($application_table_header) ? $application_table_header : null);
else : ?>
	<h3>No Applications found</h3>
<?php endif;