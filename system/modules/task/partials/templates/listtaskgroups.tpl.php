<?php if (!empty($taskgroups)) : ?>
	<div class="taskgroups_container">
		<?php foreach($taskgroups as $taskgroup) : ?>
			<?php if ($taskgroup->canView(AuthService::getInstance($w)->user())) : ?>
				<div class="row-fluid"><div class="columns small-12"><?php echo $w->partial("showtaskgroup", array("taskgroup" => $taskgroup, "redirect" => (!empty($redirect) ? $redirect : "/")), "task"); ?></div></div>
			<?php endif; ?>
			<?php if ($taskgroup->getCanICreate()) : ?> 
				<div class="row-fluid"><div class="columns small-12"><a target="_blank" href="/task/edit/?gid=<?php echo $taskgroup->id; ?>">New Task</a> </div></div> 
			<?php endif; ?>
		<?php endforeach; ?>
	</div>	
			
<?php endif; ?>
