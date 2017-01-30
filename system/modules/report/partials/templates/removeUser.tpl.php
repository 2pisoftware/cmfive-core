<?php if (!empty($owned_reports) || !empty($member_reports)) : ?>
	<h4><?php echo _e('Report'); ?></h4>
	<?php if (!empty($owned_reports)) : ?>
		<p><?php echo _e('This user owns'); ?> <?php echo count($owned_reports); ?> <?php echo _e('report'); ?><?php echo count($owned_reports) == 1 ? '' : 's'; ?></p>
	<?php endif;
	if (!empty($member_reports)) : ?>
		<p><?php echo _e('This user is a member of'); ?> <?php echo count($member_reports); ?> <?php echo _e('report'); ?><?php echo count($member_reports) == 1 ? '' : 's'; ?></p>
	<?php endif; ?>

	<?php echo Html::box("/report-user/reassign/" . $user->id . "?redirect=" . urlencode("/admin-user/remove/" . $user->id), __("Reassign report(s)"), true, false, null, null, null, null, "button warning expand"); ?>
<?php endif;
