<?php echo Html::box("/timelog/edit?class={$class}&id={$id}" . (!empty($redirect) ? "&redirect=$redirect" : ''), "Add new timelog", true); ?>
<h4 style="display: inline; padding: 0px 5px;" class="right">
	<?php echo TaskService::getInstance($w)->getFormatPeriod($total); ?>
</h4>

<?php if (!empty($timelogs)) : ?>
	<table class='tablesorter small-12'>
		<thead><tr><th width="10%">Name</th><th width="15%">From</th><th width="15%">To</th><th width="5%">Duration</th><th width="10%">Time type</th><th width="25%">Description</th><th width="20%">Actions</th></tr></thead>
		<tbody>
			<?php foreach($timelogs as $timelog) : ?>
				<tr class='timelog' data-id="<?php echo $timelog->id; ?>" >
					<td><?php echo $timelog->getFullName(); ?></td>
					<td><?php echo formatDate($timelog->dt_start, "d-m-Y H:i:s"); ?></td>
					<td><?php echo formatDate($timelog->dt_end, "d-m-Y H:i:s"); ?></td>
					<td><?php echo($timelog->isRunning() ? "See Timer" : $timelog->getHoursWorked() . ':' . str_pad($timelog->getMinutesWorked(), 2, '0', STR_PAD_LEFT));?></td>
					<td><?php echo $timelog->time_type; ?></td>
					<td><pre class="break-pre" style="font-family: sans-serif;"><?php echo $timelog->getComment()->comment; ?></pre></td>
					<td>
						<?php
                if ($timelog->canEdit(AuthService::getInstance($w)->user())) {
                    echo Html::box('/timelog/edit/' . $timelog->id . (!empty($redirect) ? "?redirect=$redirect" : ''), 'Edit', true);
                    echo Html::box('/timelog/move/' . $timelog->id . (!empty($redirect) ? "?redirect=$redirect" : ''), 'Move', true);
                }
                if ($timelog->canDelete(AuthService::getInstance($w)->user())) {
                    $confirmation_message = implode("", $w->callHook("timelog", "before_display_timelog", $timelog));
                    echo Html::b('/timelog/delete/' . $timelog->id . (!empty($redirect) ? "?redirect=$redirect" : ''), 'Delete', empty($confirmation_message) ? 'Are you sure you want to delete this timelog?' : $confirmation_message, null, false, "warning");
                }
			    ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif;
