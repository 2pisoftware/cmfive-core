<?php
?>

		   <form id="leadfilter" action="<?php $webroot."/task/taskweek"; ?>" method="POST">
		   	<input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
				<fieldset style="margin-top: 10px;">
					<legend>Search Tasks</legend>
						<table cellpadding=2 cellspacing=2 border=0>
							<tr>
								<td align=right style="padding-left:20px;">Groups</td><td><?php echo $taskgroups; ?></td>
								<td align=right style="padding-left:20px;">User</td><td><?php echo $assignee; ?></td>
								<td align=right style="padding-left:20px;">From Date</td><td><input class="date_picker" type="text" name="dt_from" value="<?php echo $reqdtFrom ?>" size="" id="dt_from"/><script>$('#dt_from').datepicker({dateFormat: 'dd/mm/yy'});$('#dt_from').keyup( function(event) { $(this).val('');}); </script></td>
								<td align=right style="padding-left:20px;">To Date</td><td><input class="date_picker" type="text" name="dt_to" value="<?php echo $reqdtTo ?>" size="" id="dt_to"/><script>$('#dt_to').datepicker({dateFormat: 'dd/mm/yy'});$('#dt_to').keyup( function(event) { $(this).val('');}); </script></td>
								<td align=right><input type="submit" name="taskFilter" value=" Search Tasks "/></td>
							</tr>
						</table>
				</fieldset>
			</form>
		    <p>
			<?php echo $taskweek; ?>

<script language="javascript">
$(document).ready(function() {
	$("#dt_from").val("<?php echo $from ?>");
	$("#dt_to").val("<?php echo $to ?>");
});
</script>