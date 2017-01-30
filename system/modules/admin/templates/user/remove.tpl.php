<?php echo Html::b("/admin/users", __("Back to user list")); ?>
<div class='row-fluid clearfix'>
	<?php echo $hook_output; ?>
</div>
<div class="panel">
	<h4><?php _e('Deactivate user'); ?></h4>
	<?php echo Html::b("/admin/userdel/" . $user->id, __("Delete user"), __("Are you sure you want to delete this user?"), null, false, "warning"); ?>
</div>
