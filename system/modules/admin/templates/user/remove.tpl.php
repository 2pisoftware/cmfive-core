<?php echo Html::b("/admin/users", "Back to user list"); ?>
<div class='row-fluid clearfix'>
	<?php echo $hook_output; ?>
</div>
<div class="panel">
	<h4>Deactivate user</h4>
	<?php echo Html::b("/admin/userdel/" . $user->id, "Delete user", "Are you sure you want to delete this user?", null, false, "warning"); ?>
</div>