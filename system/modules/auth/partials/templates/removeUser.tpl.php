<?php if (!empty($user) && !empty($user_group_member)) : ?>
	<h4><?php _e('Auth'); ?></h4>

	<p><?php _e('This user is a member of'); ?> <?php echo count($user_group_member) . __(' group') . (count($user_group_member) == 1 ? '' : 's'); ?></p>
	
	<form action="/auth-user/unassign/<?php echo $user->id; ?>?redirect=<?php echo urlencode($redirect); ?>" method="POST">
		<button class="button warning expand" onclick="toggleModalLoading();" type="submit"><?php _e('Unassign from groups'); ?></button>
	</form>
<?php endif;
