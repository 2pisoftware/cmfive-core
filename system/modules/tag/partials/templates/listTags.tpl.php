<?php if (AuthService::getInstance($w)->user()->hasRole('tag_user') && $object->canView(AuthService::getInstance($w)->user())) : ?>
	<div class="tags-container" data-tag-id="<?php echo $tag_obj_id ?>" data-modal-target="/tag/changeTags/<?php echo get_class($object); ?>/<?php echo $object->id; ?>">
		<div class="loader d-none"></div>
		<div id="tags_<?php echo $tag_obj_id ?>" class="shown_tags">
			<?php
			if (empty($tags["display"])) : ?>
				<span class="bg-info tag-small text-light">
					No tags
				</span>
			<?php endif; ?>

			<?php foreach ($tags["display"] as $tag) : ?>
				<span class="bg-info tag-small text-light">
					<?php echo StringSanitiser::sanitise($tag["tag"]); ?>
				</span>
			<?php endforeach; ?>
		</div>
		<div class="show_more tag-small bg-info <?php echo empty($tags["hover"]) ? "d-none" : "" ?>">...</div>
		<div id="hidden_tags_<?php echo $tag_obj_id ?>" class="hidden_tags">
			<?php foreach ($tags["hover"] as $tag) : ?>
				<span class="bg-info tag-small text-light">
					<?php echo StringSanitiser::sanitise($tag["tag"]); ?>
				</span>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif;
