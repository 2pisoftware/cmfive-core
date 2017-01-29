<?php echo $form; ?>

<script>
	// Validate file size on submit
	$('#file_form').on('submit', function(event) {
		if (typeof FileReader !== "undefined") {
		    var size = document.getElementById('file').files[0].size;
		    var max_size_php = <?php echo @$w->File->getMaxFileUploadSize() ? : (2 * 1024 * 1024); ?>;
		    
		    // check file size
		    if (size > max_size_php) {
		    	alert('Size of file is too big! Max size is ' + formatBytes(max_size_php));
		    	event.preventDefault();
		    	return false;
		    }
		}

		return true;
	});
</script>