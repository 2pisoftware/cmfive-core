<?php if (!empty($statuses)) :?>

	<table class="tablesorter">
		<thead><tr><th>Processor ID</th><th>Message</th><th>Was Successful</th><th>Actions</th></tr></thead>
		<tbody>
			<?php foreach($statuses as $s) : ?>
				<?php $message = $w->Channel->getMessage($s->message_id); ?>
				<tr>
					<td><?php echo $s->processor_id; ?></td>
					<td><?php echo $s->message; ?></td>
					<td><?php echo $s->is_successful ? "Yes" : "No"; ?></td>
					<td><?php if (!empty($message->channel_id)) echo Html::a("/channels/process/{$message->channel_id}", "Rerun Process"); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

<?php endif; ?>