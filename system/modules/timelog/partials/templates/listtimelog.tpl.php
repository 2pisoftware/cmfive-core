<?php echo Html::box("/timelog/edit?class={$class}&id={$id}" . (!empty($redirect) ? "&redirect=$redirect" : ''), "Add new timelog", true); ?>
<?php if (!empty($billable_hours)) : ?>
  <details style="user-select: none;" class="right">
    <summary style="display: flex; cursor: pointer; justify-content: right;">
      <h4 class="right" style="margin-bottom: 5px;">
        <?php echo TaskService::getInstance($w)->getFormatPeriod($total); ?>
      </h4>
      <span class="icon" style="padding: 6px;">&dtrif;</span>
    </summary>
    <style>
      details[open] summary span {
        transform: rotate(180deg) translate(0px, 5px)
      }
    </style>
    <p style="font-size: 11px; text-align: right; margin-bottom: 10px;">
      <?php
        if ($billable_hours) {
            echo 'Billable: ' . $billable_hours['Billable'] . '<br>Non-Billable: ' . $billable_hours['Non-Billable'];
        }
      ?>
    </p>
  </details>
<?php else: ?>
  <h4 class="right" style="margin-bottom: 5px">
        <?php echo TaskService::getInstance($w)->getFormatPeriod($total); ?>
  </h4>
<?php endif; ?>

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
