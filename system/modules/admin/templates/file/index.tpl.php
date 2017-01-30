<?php if (!empty($attachments)) : ?>
	
	<?php foreach($attachments as $attachment_adapter => $sorted_attachments) : ?>
		<h3>Files on <?php echo $attachment_adapter; ?></h3>
		<table class="small-12 columns " style="margin-bottom: 50px;">
			<thead>
				<tr><th wdith="30%"><?php _e('Title'); ?></th><th width="20%"><?php _e('Object'); ?></th><th width="20%"><?php _e('Location'); ?></th><th width="30%"><?php _e('Actions'); ?></th></tr>
			</thead>
			<tbody>
				<?php foreach($sorted_attachments as $attachment) : ?>
					<tr>
						<td><?php echo $attachment->title; ?></td>
						<td><?php echo $attachment->parent_table . ' [' . $attachment->parent_id . ']'; ?></td>
						<td><?php echo $attachment->adapter; ?></td>
						<td>
							<div class="row-fluid">
								<div class="small-6 columns">
									<a class="button tiny expand" href="<?php echo $attachment->getViewUrl() ?>" target="_new" ><?php _e('View'); ?></a>
								</div>
								<div class="small-6 columns">
									<button href="#" data-dropdown="move_to_<?php echo $attachment->id; ?>" aria-controls="move_to_<?php echo $attachment->id; ?>" aria-expanded="false" class="button dropdown expand"><?php _e('Move to '); ?></button><br>
									<ul id="move_to_<?php echo $attachment->id; ?>" data-dropdown-content class="f-dropdown" aria-hidden="true" style="z-index:1000;">
										<?php if (!empty($adapters)) : ?>
											<?php foreach($adapters as $adapter) : ?>
												<?php if ($adapter != $attachment_adapter && Config::get('file.adapters.'.$adapter.'.active')===true) : ?>
													<li><a id="<?php echo $attachment->id; ?>" href="#"><?php echo $adapter; ?></a></li>
												<?php endif; ?>
											<?php endforeach; ?>
										<?php endif; ?>
									</ul>
								</div>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endforeach;
else : ?>
		<h3><?php _e('No attachment available'); ?></h3>
<?php endif; ?>

<script>
	
	$(".f-dropdown a").click(function() {
		window.location.href="/admin-file/index/" + $(this).attr("id") + "?adapter=" + $(this).html();
	});
	
</script>
