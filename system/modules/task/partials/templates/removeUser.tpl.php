
<?php if (!empty($tasks) || !empty($task_group_membership)) : ?>
	<h4><?php _e('Task'); ?></h4>

	<form action="/task-user/unassign/<?php echo $user->id; ?>?redirect=<?php echo urlencode($redirect); ?>" method="POST">
		<?php if (!empty($tasks)) : ?>
			<p>This user has <strong><?php echo count($tasks); ?></strong> task<?php echo (count($tasks) == 1 ? '' : 's'); ?> assigned to them.</p>
		<?php endif; ?>
		
		<?php if (!empty($task_group_membership)) : ?>
			<p><?php _e('This user is a member of'); ?><strong><?php echo count($task_group_membership); ?></strong> <?php _n('task group','task groups',count($task_group_membership)); ?>
				<?php if ($default_taskgroup_assignee > 0): ?> 
					<?php _e('and they are the default assignee for'); ?><strong><?php echo $default_taskgroup_assignee; ?></strong> <?php _e('of these'); ?>
				<?php endif; ?>
			</p>
			<?php if (!empty($single_member_taskgroups)) : ?>
			<div class="alert-box alert">
				<?php _e('WARNING: Revoking this users Taskgroup membership will leave'); ?> <strong><?php echo count($single_member_taskgroups) . _n("taskgroup","taskgroups",count($single_member_taskgroups)); ?></strong>
				<?php _e('without any members!'); ?>
			
				<br/><?php _e('Please add members to the following Taskgroups before proceeding:'); ?><br/>
				
				<?php foreach($single_member_taskgroups as $single_member_taskgroup) :
					echo Html::b("/task-group/viewmembergroup/" . $single_member_taskgroup->id . "#members", $single_member_taskgroup->title, null, null, true, "warning") . '<br/>';
				endforeach; ?>
			</div>
			<?php endif; ?>
		<?php endif; ?>

		<p><?php _e('The following actions will be carried out:'); ?></p>
		<ul>
			<li><?php _e('Remove user membership from any taskgroups'); ?></li>
			<li><?php _e('Remove user from any taskgroup default assignee (WARNING: result will be that taskgroup has no default assignee)'); ?></li>
			<li><?php _e('Reassign any tasks assigned to the user to the default user (As with above, if the user is assigned and also the default assignee, the result will be tasks become unassigned)'); ?></li>
		</ul>

		<button class="button warning expand" onclick="toggleModalLoading();" type="submit"><?php _e('Remove from Task'); ?></button>
	</form>

<?php endif;
	
