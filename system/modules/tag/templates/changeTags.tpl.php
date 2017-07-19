<form action='/tag/changeTags/<?php echo $object_class; ?>/<?php echo $id; ?>'>
	<input id='display_tags_<?php echo $object_class; ?>_<?php echo $id; ?>' value='<?php echo implode(',', array_map(function($tag) {return $tag->id;}, $tags ? : [])); ?>' />
</form>
<script>

	var $select_<?php echo $id; ?> = $('#display_tags_<?php echo $object_class; ?>_<?php echo $id; ?>').selectize({
		plugins: ['remove_button', 'optgroup_columns'],
		options: [
			<?php 
			$tags_to_display = array_merge(
				array_map(function($object_tag) {
					return ['id' => $object_tag->id, 'tag_type' => $object_tag->obj_class, 'tag' => $object_tag->tag];
				}, $object_tags ? : []),
				array_map(function($all_tag) {
					return ['id' => $all_tag->id, 'tag_type' => 'All', 'tag' => $all_tag->tag];
				}, $all_tags ? : [])
			);
			
			foreach($tags_to_display as $tag_to_display) {
				echo json_encode($tag_to_display, JSON_FORCE_OBJECT) . ',';
			} ?>
		],
		optgroups: [
			{$order: 1, id: '<?php echo $object_class; ?>', name: 'Existing tags on a <?php echo $object_class; ?>'},
			{$order: 2, id: 'All', name: 'All other tags'}
		],
		labelField: 'tag',
		valueField: 'id',
//		persist: false,
		optgroupField: 'tag_type',
		optgroupLabelField: 'name',
		optgroupValueField: 'id',
		searchField: ['tag'],
		openOnFocus: true,
		lockOptgroupOrder: true,
		hideSelected: false,
		duplicates: true
	});
	
//	var values = [<?php echo implode(',', array_map(function($tag) {return $tag->id;}, $tags ? : [])); ?>];
//	$select_<?php echo $id; ?>[0].selectize.setValue(values);

</script>