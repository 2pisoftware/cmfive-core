<div id="permissions_list" style="width:70%;"><?php echo $permission?></div>

<script type="text/javascript">
	$("#goBack").click(function(){
            history.back();
	});

	var maskedArray = <?php echo $groupRoles; ?>;
	for(var i in maskedArray) {
            $("#check_" + maskedArray[i]).attr("disabled", "disabled");
	}
</script>