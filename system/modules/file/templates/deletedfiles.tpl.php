<h1>Deleted files</h1>
<?php if (!empty($deleted_files)) : ?>
	<table class="small-12">
		<thead>
			<tr>
				<td>File</td>
				<td>Path</td>
				<td>Actions</td>
			</tr>
		</thead>
		<tbody>
			<div class="container-fluid">
				<?php foreach ($deleted_files as $deleted_file) : ?>
					<div class="row">
						<div class="col">
							<?php echo $deleted_file->filename; ?>
						</div>
						<div class="col">
							<?php echo $deleted_file->fullpath; ?>
						</div>
						<div class="col">
							<!-- Modal -->
							<div class="modal fade" id="viewDocument" tabindex="-1" aria-labelledby="viewDocument" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="viewDocumentLabel">View Document</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>
										<div class="modal-body">
											<img src="<?php echo $deleted_file->fullpath; ?>" alt="<?php echo $deleted_file->title; ?>" />
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>
							<!-- <div id="attachment_modal_<?php echo $deleted_file->id; ?>" class="reveal-modal" data-reveal role="dialog">
								<div class="row-fluid panel" style="text-align: center;">
									<img src="/file/atfile/<?php echo $deleted_file->id; ?>" alt="<?php echo $deleted_file->title; ?>" />
								</div>
							</div> -->
							<!--<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewDocument">View</button>
							<button href="#" data-reveal-id="attachment_modal_<?php echo $deleted_file->id; ?>" class="button">View</button> -->

							<?php
							$action = "";
							$action .= HtmlBootstrap5::b("/file/atfile/" . $deleted_file->id, "View", null, null, false, "btn btn-sm btn-primary");
							$action .= HtmlBootstrap5::b("/file-attachment/restore/" . $deleted_file->id . "?redirect_url=" . urlencode("/file/deletedfiles"), "Restore file", "Are you sure you want to restore this file?", null, false, "btn btn-sm btn-secondary");
							$action .= HtmlBootstrap5::b("/file-attachment/delete/" . $deleted_file->id . "?redirect_url=" . urlencode("/file/deletedfiles"), "Permanently Delete", "Are you sure you want to delete this file?", null, false, "btn btn-sm btn-danger");
							echo HtmlBootstrap5::buttonGroup($action); ?>
						</div>
					</div>
				<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<p>There are no soft deleted files</p>
<?php endif;
