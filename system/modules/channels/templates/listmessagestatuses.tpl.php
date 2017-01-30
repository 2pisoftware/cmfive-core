<?php if (!empty($statuses)) :?>

	<table class="tablesorter">
		<thead><tr><th><?php _e('Processor ID'); ?></th><th><?php _e('Message'); ?></th><th><?php _e('Was Successful'); ?></th><th><?php _e('Actions'); ?></th></tr></thead>
		<tbody>
			<?php foreach($statuses as $s) : ?>
				<?php $message = $w->Channel->getMessage($s->message_id); ?>
				<tr>
					<td><?php echo $s->processor_id; ?></td>
					<td><?php echo $s->message; ?></td>
					<td><?php echo $s->is_successful ? __("Yes") : __("No"); ?></td>
					<td><?php if (!empty($message->channel_id)) echo Html::a("/channels/process/{$message->channel_id}", __("Rerun Process")); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php endif; ?>
