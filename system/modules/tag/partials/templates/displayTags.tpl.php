<style>
	.tag_container {
		display: inline-block;
		position: relative;
		height: 40px;
		width: 200px;
		top: -11px;
	}
	
	.tag_container .tag_header {
		font-size: 12px;
		display: block;
	}
	
	.tag_container .tag_container_inner {
		position: absolute;
		bottom: 0px; left: 0px;
		width: 100%;
	}
</style>
<div class='tag_container'>
	<div class='tag_header'>Tags</div>
	<div class='tag_container_inner'>
		<?php if (!empty($tags)) : ?>
			<?php foreach($tags as $tag) : ?>
				<span class='info label'><?php echo $tag->tag; ?></span>
			<?php endforeach; ?>
		<?php else: ?>
			<span class='info label'>No tags</span>
		<?php endif; ?>
	</div>
</div>
<div id='tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal' class='reveal-modal small' data-reveal>
	<a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<script>
	$('.tag_container').click(function() {
		$('#tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal').foundation('reveal', 'open', {'animation_speed': 1, 'url' : '/tag/changeTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>'});
		return false;
	});
</script>