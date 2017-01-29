<?php echo $form; ?>

<script type="text/javascript">
	
	jQuery(".folder_link").click(function(e) {
		e.preventDefault();

		$.ajax({url: "/channels-email/ajax_getfolderlist", data: $("#channelform").serialize(), type: "GET",
	        success: function(data) {
	            var parsed = JSON.parse(data);
	            if (parsed.success == false) {
	            	alert("Could not get folder list");
	            } else {
	            	for(var i in parsed.response) {
	            		$("#folder").append("<option value='" + parsed.response[i] + "'>" + parsed.response[i] + "</option>");
	            	}
	            }
	        },
	        error: function(e) {
	            alert("Could not get folder list");
	        }
	    });
	});

</script>