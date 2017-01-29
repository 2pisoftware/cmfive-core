<?php if (!empty($info)) : ?>
	<div class="row-fluid">
		<h3 style="text-align: center;"><?php echo $info['username']; ?> • <?php echo $info['stats']['all_time']['sent']; ?> sent since <?php echo date('d/m/Y', strtotime($info['created_at'])); ?> • <a href="/admin/testemail" target="_blank">Test email</a></h3>
	</div>
	<div class="row-fluid clearfix" style="text-align: center;">
		<h4 style="text-align: center;">Stats for today</h4>
		<div class="small-12 medium-4 panel columns" style="border: 1px solid #444; border-radius: 5px;">
			<h4 style="border-bottom: 1px solid black;">QUOTA</h4>
			<font style="font-size: 18pt; color: #444;"><?php echo $info['hourly_quota']; ?>/hr</font>
		</div>
		<div class="small-12 medium-4 panel columns" style="border: 1px solid #444; border-radius: 5px;">
			<h4 style="border-bottom: 1px solid black;">SENT</h4>
			<font style="font-size: 18pt; color: #444;"><?php echo $info['stats']['today']['sent']; ?></font>
		</div>
		<div class="small-12 medium-4 panel columns" style="border: 1px solid #444; border-radius: 5px;">
			<h4 style="border-bottom: 1px solid black;">BOUNCED/REJECTED</h4>
			<font style="font-size: 18pt; color: #444;">
				<?php echo $info['stats']['today']['hard_bounces'] + $info['stats']['today']['soft_bounces'] + $info['stats']['today']['rejects']; ?>
			</font>
		</div>
	</div>
<?php endif; ?>

<?php if (!empty($messages)) : ?>
	<table class="small-12 columns">
		<thead><tr><th>Time</th><th>Status</th><th>Email</th><th>Subject</th><th>Opens</th></tr></thead>
		<tbody>
			<?php foreach($messages as $message) : ?>
				<tr>
					<td><?php echo date('H:i d-m-Y', $message['ts']); ?></td>
					<td><?php echo $message['state']; ?></td>
					<td><?php echo $message['email']; ?></td>
					<td><?php echo $message['subject']; ?></td>
					<td><?php echo $message['opens']; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif;
