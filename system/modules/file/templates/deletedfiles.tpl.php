<h1>Deleted files</h1>
<?php if (!empty($deleted_files)) : ?>
	<table class="table-striped">
		<thead>
			<tr>
				<th scope="col">File</th>
				<th scope="col">Path</th>
				<th scope="col">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($deleted_files as $deleted_file) : ?>
				<tr>
					<td><?php echo $deleted_file->filename; ?></td>
					<td><?php echo $deleted_file->fullpath; ?></td>
					<td><?php
						$action = HtmlBootstrap5::b("/file/atfile/" . $deleted_file->id, "View", null, null, true, "btn btn-sm btn-primary");
						$action .= HtmlBootstrap5::b("/file-attachment/restore/" . $deleted_file->id . "?redirect_url=" . urlencode("/file/deletedfiles"), "Restore file", "Are you sure you want to restore this file?", null, false, "btn btn-sm btn-secondary");
						$action .= HtmlBootstrap5::b("/file-attachment/delete/" . $deleted_file->id . "?redirect_url=" . urlencode("/file/deletedfiles"), "Permanently Delete", "Are you sure you want to delete this file?", null, false, "btn btn-sm btn-danger");
						echo HtmlBootstrap5::buttonGroup($action); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<p>There are no soft deleted files</p>
<?php endif;
