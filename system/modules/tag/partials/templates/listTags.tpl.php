<?php if ($w->Auth->user()->hasRole('tag_user') && $object->canView($w->Auth->user())) : ?>

	<tag object-class="<?php echo get_class($object); ?>" 
		 object-id="<?php echo $object->id; ?>" 
		 :preload-tags='<?php echo json_encode(array_merge($tags['display'], $tags['hover'])); ?>' 
		 :can-edit="<?php echo $object->canEdit($w->Auth->user()) ? 'true' : 'false'; ?>"></tag>

<?php endif;
