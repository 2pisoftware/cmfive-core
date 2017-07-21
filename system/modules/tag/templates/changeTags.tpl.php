<style>

	.display_tags_<?php echo $object_class; ?>_<?php echo $id; ?>-selectized {
		display: relative !important;
	}
	
</style>

<h3 style='text-align: center;'>Tags for <span style='color: #444; font-weight: bold;'><?php echo $object_class; ?>: <?php echo $object->getSelectOptionTitle(); ?></span></h3>
	<div class='row-fluid'>
		<div class='small-12'>
			<?php echo (new \Html\Form\InputField([
				'id' => 'display_tags_' . $object_class . '_' . $id,
				'name' => 'tags',
				'value' => implode(',', array_map(function($tag) {return $tag->id;}, $tags ? : []))
			])); ?>
		</div>
	</div>
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
			{$order: 2, id: 'All', name: 'All tags'}
		],
		labelField: 'tag',
		valueField: 'id',
//		persist: false,
		optgroupField: 'tag_type',
		optgroupLabelField: 'name',
		optgroupValueField: 'id',
		searchField: ['tag'],
//		openOnFocus: true,
		lockOptgroupOrder: true,
		hideSelected: false,
		duplicates: true,
		create:function (input, callback){
			$.ajax({
				url: '/tag/createTag/<?php echo $object_class; ?>/<?php echo $id; ?>?tag=' + input,
				type: 'GET',
				success: function (result) {
					debugger;
					if (result) {
						var j_result = JSON.parse(result);
						callback({ id: j_result.id, tag: j_result.tag, type: '<?php echo $object_class; ?>' });
					}
				}
			});
		},
		onItemAdd: function (tag_id, event) {
			debugger;
		},
		onItemRemove: function(tag_id, event) {
			debugger;
		}
	});
	
//	var values = [<?php echo implode(',', array_map(function($tag) {return $tag->id;}, $tags ? : [])); ?>];
//	$select_<?php echo $id; ?>[0].selectize.setValue(values);

</script>