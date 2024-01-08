<div class="row-fluid">
	<div class="small-12">
		<?php
		echo HtmlBootstrap5::b('/form-application/edit', 'Create Application', null, null, false, "btn-sm btn-primary");
		echo HtmlBootstrap5::box('/form-application/import', 'Import Application', true, false, null, null, "isbox", null, "btn-sm btn-primary");
		?>
	</div>
</div>

<?php
if (!empty($application_table_data)) {
	echo HtmlBootstrap5::table($application_table_data, null, 'tablesorter', !empty($application_table_header) ? $application_table_header : null);
}
?>