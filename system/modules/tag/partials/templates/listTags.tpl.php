<?php if (AuthService::getInstance($w)->user()->hasRole('tag_user') && $object->canView(AuthService::getInstance($w)->user())) : ?>

<?php if ($object->canEdit(AuthService::getInstance($w)->user())) : ?>
	<div id='tag_<?php echo get_class($object); ?>_<?php echo $object->id; ?>_modal' class='reveal-modal medium' data-reveal>
		<a class="close-reveal-modal" aria-label="Close">&#215;</a>
	</div>
<?php endif; ?>

<!-- VUE IMPLEMENTATION -->
<div class='tag_container' id="tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>">
	<div class='tag_show_container' v-if='!loading && display_tags && display_tags.length'>
		<span class='info label' v-for='tag in display_tags'>{{ tag.tag }}</span>
		<span class='count_hover_tags' v-if='hidden_tags && hidden_tags.length'>+ {{ hidden_tags.length }}</span>
	</div>
	<div class="show_hover_<?php echo get_class($object); ?>_<?php echo $object->id; ?>" v-if='!loading && hidden_tags && hidden_tags.length'>
		<span class='info label' v-for='tag in hidden_tags'>{{ tag.tag }}</span>
	</div>
	<span class='secondary label' v-if='!loading && (!display_tags || !display_tags.length)'>No tags</span>
	<div v-if='loading' class='loader'></div>
</div>
<script>
	
	var tag_vue_instance_<?php echo get_class($object); ?>_<?php echo $object->id; ?> = new Vue({
		el: '#tag_container_<?php echo get_class($object); ?>_<?php echo $object->id; ?>',
		data: {
			display_tags: <?php echo json_encode($tags['display']); ?>,
			hidden_tags: <?php echo json_encode($tags['hover']); ?>,
			loading: false
		},
		methods: {
			getTags: function() {
				var _this = this;
				_this.loading = true;
				$.ajax({
					url: '/tag/ajaxGetTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>',
					method: 'GET',
					success: function(response) {
						var tags = JSON.parse(response);
						_this.display_tags = tags.display;
						_this.hidden_tags = tags.hover;
					},
					complete: function(response) {
						_this.loading = false;
					}
				});
			}
		}
	});
	
	<?php if ($object->canEdit(AuthService::getInstance($w)->user())) : ?>
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
	<?php endif; ?>
</script>

<?php endif;