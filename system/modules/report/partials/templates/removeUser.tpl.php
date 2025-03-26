<?php if (!empty($owned_reports) || !empty($member_reports)) : ?>
	<h4>Report</h4>
	<?php if (!empty($owned_reports)) : ?>
		<p>This user owns <?php echo count($owned_reports); ?> report<?php echo count($owned_reports) == 1 ? '' : 's'; ?></p>
	<?php endif;
	if (!empty($member_reports)) : ?>
		<p>This user is a member of <?php echo count($member_reports); ?> report<?php echo count($member_reports) == 1 ? '' : 's'; ?></p>
	<?php endif; ?>

	<?php echo HtmlBootstrap5::box("/report-user/reassign/" . $user->id . "?redirect=" . urlencode("/admin-user/remove/" . $user->id), "Reassign report(s)", true, false, null, null, null, null, "button warning expand"); ?>
<?php endif;
