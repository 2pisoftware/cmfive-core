<div class="row-fluid">
	<div class="small-12">
		<?php echo HtmlBootstrap5::box("/form/edit", "Add a form", true, false, null, null, "isbox", null, "btn-sm btn-primary"); ?>
		<?php echo HtmlBootstrap5::box("/form/import", "Import a form", true, false, null, null, "isbox", null, "btn-sm btn-primary"); ?>
	</div>
</div>

<?php
if (!empty($forms)) {
	$header = ["Title", "Description", "Actions"];
	$data = [];
	foreach ($forms as $form) {
		$row = [];
		$row[] = $form->toLink();
		$row[] = $form->description;
		$row[] = HtmlBootstrap5::buttonGroup(
			implode(
				"",
				[
					HtmlBootstrap5::box("/form/edit/$form->id", "Edit", true, false, null, null, "isbox", null, "btn-sm btn-secondary"),
					HtmlBootstrap5::b("/form/export/$form->id", "Export", null, null, false, "btn-sm btn-info"),
					HtmlBootstrap5::b("/form/delete/$form->id", "Delete", "Are you sure you want to delete this form?", null, false, "btn-sm btn-danger"),
				]
			)
		);
		$data[] = $row;
	}

	echo HtmlBootstrap5::table($data, null, "tablesorter", $header);
}
?>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		var table = document.getElementsByTagName('table')[0];
		var thElements = table.getElementsByTagName('th');

		if (thElements.length >= 3) {
			thElements[0].style.width = '30%';
			thElements[1].style.width = '50%';
			thElements[2].style.width = '20%';
		}
	});
</script>