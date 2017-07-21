<style>
	
	.tag_container {
		display: inline-block;
	}

</style>
<div class='tag_container'>
	<!--<div class='tag_header'>Tags</div>-->
	<div class='tag_container_inner'>
		<?php if (!empty($tags)) : ?>
			<?php foreach($tags as $tag) : ?>
				<span class='info label'><i class='fa fa-tag'></i><?php echo $tag->tag; ?></span>
			<?php endforeach; ?>
		<?php else: ?>
			<span class='secondary label'>No tags</span>
		<?php endif; ?>
	</div>
</div>
<div id='tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal' class='reveal-modal medium' data-reveal>
	<a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<script>
	$('.tag_container').click(function() {
		$('#tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal').foundation('reveal', 'open', {'animation_speed': 1, 'url' : '/tag/changeTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>'});
		return false;
	});
</script>