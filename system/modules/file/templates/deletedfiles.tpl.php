<h1>Deleted files</h1>
<?php if (!empty($deleted_files)) : ?>
	<table class="small-12">
		<thead>
			<tr><td>File</td><td>Path</td><td>Actions</td></tr>
		</thead>
		<tbody>
			<?php foreach($deleted_files as $deleted_file) : ?>
				<tr>
					<td><?php echo $deleted_file->filename; ?></td>
					<td><?php echo $deleted_file->fullpath; ?></td>
					<td>
						<div id="attachment_modal_<?php echo $deleted_file->id; ?>" class="reveal-modal" data-reveal role="dialog">
							<div class="row-fluid panel" style="text-align: center;">
								<img src="/file/atfile/<?php echo $deleted_file->id; ?>" alt="<?php echo $deleted_file->title; ?>" />
							</div>
						</div>
						<button href="#" data-reveal-id="attachment_modal_<?php echo $deleted_file->id; ?>" class="button">View</button>
						<?php echo Html::b("/file-attachment/restore/" . $deleted_file->id . "?redirect_url=" . urlencode("/file/deletedfiles"), "Restore file", "Are you sure you want to restore this file?"); ?>
						<?php echo Html::b("/file-attachment/delete/" . $deleted_file->id . "?redirect_url=" . urlencode("/file/deletedfiles"), "Permanently Delete", "Are you sure you want to delete this file?"); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
	<p>There are no soft deleted files</p>
<?php endif;