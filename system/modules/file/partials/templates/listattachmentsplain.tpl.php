<style>
	
	div.image-container {
		width: auto;
		height: 220px;
		position: relative;
		margin-left: auto;
		margin-right: auto;
		overflow: hidden;
		padding: 10px;
		border: 1px solid #444;
		margin: 5px;
		background-color: #efefef;
	}

	div.image-container-overlay {
		background-color: rgba(0, 0, 0, 0.7);
		position: absolute;
		margin: auto;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		z-index: 100;
		display: none;
		padding: 10px;
	}
	
	div.image-container-overlay > .row-fluid {
		height: 75px;
	}
 	
	div.image-container-overlay:hover {
		display: block;
	} 
	 
	img.image-cropped {
		position: absolute;
		margin: auto;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
	}
	
</style>
<div data-object="<?php echo get_class($object); ?>" data-id="<?php echo $object->id; ?>" style="display:none;"></div>

<?php
	$notImages = array();
    if (!empty($attachments)) : ?>
        <br/><br/>
        <ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">
        <?php foreach ($attachments as $attachment) : ?>
            <?php if ($attachment->isImage()) : ?>
				<li>
					<div class="image-container attachment">
						<a href="<?php  echo $attachment->getDownloadUrl() ?>" target="_new"  ><img class="image-cropped" data-caption="<?php echo $attachment->title; ?>" src="<?php echo $attachment->getThumbnailUrl(); ?>"></a>
					</div>
				</li>
            <?php else :
                $notImages[] = $attachment;
            endif;
        endforeach; ?>
        </ul>
    <?php endif;

	if (!empty($notImages)) : ?>
		<table class="tablesorter">
			<thead>
				<tr>
					<th>Filename</th>
					<th>Title</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($notImages as $att): ?>
					<tr class="attachment">
						<td>
							<a target="_blank" href="<?php echo WEBROOT; ?>/file/atfile/<?php echo $att->id; ?>/<?php echo $att->filename; ?>"><?php echo $att->filename; ?></a>
						</td>
						<td><?php echo $att->title; ?></td>
						<td><?php echo $att->description; ?></td>                    
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<script>
		
		$(document).ready(function() {
			$(".image-container-overlay button").removeClass("tiny");
			$(".image-container").hover(function() {
				$(".image-container-overlay", this).stop().fadeIn("fast");
			}, function() {
				$(".image-container-overlay", this).stop().fadeOut("fast");
			});
		});

	</script>
