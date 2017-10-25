<?php echo $form; ?>

<script>
	
	$("#task-subscriber__add").submit(function() {
		if (!$("#acp_contact").val()) {
			// Validate fields
			if (!$("#firstname", $(this)).val() || !$("#lastname", $(this)).val() || !$("#email", $(this)).val() || !$("#work_number", $(this)).val()) {
				alert('All fields are required when adding a new contact');
				return false;
			}
			$("#email", $(this)).val(encodeURIComponent($("#email", $(this)).val()));
		}
	});
	
</script>
