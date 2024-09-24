<?php echo $form; ?>

<script>
    
    // $("#task-subscriber__add").submit(function() {
    // 	if (!$("#acp_contact").val()) {
    // 		// Validate fields
    // 		if (!$("#firstname", $(this)).val() || !$("#lastname", $(this)).val() || !$("#email", $(this)).val() || !$("#work_number", $(this)).val()) {
    // 			alert('All fields are required when adding a new contact');
    // 			return false;
    // 		}
    // 		$("#email", $(this)).val(encodeURIComponent($("#email", $(this)).val()));
    // 	}
    // });

    document.getElementById("task-subscriber__add").addEventListener("submit", (e) => {
        const data = new FormData(e.target);

        if (data.get("contact")) return;    // default

        if (!data.get("firstname") || !data.get("lastname") || !data.get("email") || !data.get("work_number")) {
            alert("All fields are required when adding a new contact");
            e.preventDefault();
            return false;
        }

        return true;
    })
    
</script>
