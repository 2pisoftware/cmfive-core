<?php echo HtmlBootstrap5::b("/admin/users", "Back to user list", null, "editbutton", false, "btn-sm btn-primary"); ?>
<div class='row-fluid clearfix'>
	<?php echo $hook_output; ?>
</div>
<div class="panel">
	<h4>Deactivate user</h4>
	<?php echo HtmlBootstrap5::b("/admin/userdel/" . $user->id, "Delete user", "Are you sure you want to delete this user?", null, false, "btn-sm btn-danger"); ?>
</div>