<style>

	.tag_container {
		display: inline-block;
		position: relative;
	}
	
	.tag_container:hover {
		cursor: pointer;
	}

	div[class^='show_hover'] {
		position: absolute;
		width: 200px;
		z-index: 1000;
		display: none;
	}

	span[class^='count_hover_tags'] {
		font-size: 0.8em;
	}

	#tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>:hover .show_hover_<?php echo get_class($object); ?>_<?php echo $object->id; ?> {
		display: block;
	}
	
	.tag_show_container span {
		margin-right: 5px;
	}
	
	div[class^='show_hover'] span {
		margin-right: 5px;
	}
	
	.selectize-dropdown-content {
		max-height: none;
	}
	
</style>

<div id='tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal' class='reveal-modal medium' data-reveal>
	<a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<!-- VUE IMPLEMENTATION -->
<div class='tag_container' id="tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>">
	<div class='tag_show_container' v-if='display_tags && display_tags.length'>
		<span class='info label' v-for='tag in display_tags'>{{ tag.tag }}</span>
		<span class='count_hover_tags' v-if='hidden_tags && hidden_tags.length'>+ {{ hidden_tags.length }}</span>
	</div>
	<div class="show_hover_<?php echo get_class($object); ?>_<?php echo $object->id; ?>" v-if='hidden_tags && hidden_tags.length'>
		<span class='info label' v-for='tag in hidden_tags'>{{ tag.tag }}</span>
	</div>
	<span class='secondary label' v-if='!display_tags || !display_tags.length'>No tags</span>
</div>
<script>
	
	var tag_vue_instance_<?php echo get_class($object); ?>_<?php echo $object->id; ?> = new Vue({
		el: '#tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>',
		data: {
			display_tags: [],
			hidden_tags: []
		},
		methods: {
			getTags: function() {
				let _this = this;
				$.ajax({
					url: '/tag/ajaxGetTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>',
					method: 'GET',
					success: function(response) {
						var tags = JSON.parse(response);
						_this.display_tags = tags.display;
						_this.hidden_tags = tags.hover;
					}
				});
			}
		},
		created: function() {
			this.getTags();
		}
	});
	
	$(document).ready(function() {
	
		$(document).on('close.fndtn.reveal', '[data-reveal]', function () {
			var modal = $(this);
			if (modal.attr('id') == 'tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal') {
				tag_vue_instance_<?php echo get_class($object); ?>_<?php echo $object->id; ?>.getTags();
			}
		});
			
		$('#tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>').click(function () {
			$('#tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal').foundation('reveal', 'open', {'animation_speed': 1, 'url': '/tag/changeTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>'});
			return false;
		});
	});

</script>