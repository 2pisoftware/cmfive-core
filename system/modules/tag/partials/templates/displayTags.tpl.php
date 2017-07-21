<style>
		
	.tag_container {
		display: inline-block;
		position: relative;
	}
	
	div[class^='show_hover_'] {
		position: absolute;
		width: 200px;
		z-index: 1000;
		display: none;
	}
	
	span[class^='count_hover_tags_'] {
		font-size: 0.8em;
	}
	
	#tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>:hover .show_hover_<?php echo get_class($object); ?>_<?php echo $object->id; ?> {
		display: block;
		cursor: pointer;
	}
/*	
	#tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>:hover .count_hover_tags_<?php echo get_class($object); ?>_<?php echo $object->id; ?> {
		display: none;
	}*/
	
</style>
<div class='tag_container' id='tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>'>
	<?php if (!empty($tags)) : ?>
		<div class='tag_show_container'>
			<?php foreach($tags['display'] as $tag) : ?>
				<span class='info label'><i class='fa fa-tag'></i><?php echo $tag->tag; ?></span>
			<?php endforeach; ?>
			<?php echo !empty($tags['hover']) ? '<span class="count_hover_tags_' . get_class($object) . '_' . $object->id . '">+' . count($tags['hover']) . '</span>': ''; ?>
		</div>
		<?php if (!empty($tags['hover'])) : ?>
			<div class="show_hover_<?php echo get_class($object); ?>_<?php echo $object->id; ?>">
				<?php foreach($tags['hover'] as $hover_tag) : ?>
					<span class='info label'><i class='fa fa-tag'></i><?php echo $hover_tag->tag; ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<span class='secondary label'>No tags</span>
	<?php endif; ?>
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