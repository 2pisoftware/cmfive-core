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
				array_map(function($object_tag) use ($object_class) {
					return ['id' => $object_tag->id, 'tag_type' => $object_class, 'tag' => $object_tag->tag];
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
		persist: false,
		optgroupField: 'tag_type',
		optgroupLabelField: 'name',
		optgroupValueField: 'id',
		searchField: ['tag'],
		lockOptgroupOrder: true,
		hideSelected: false,
		enableDuplicate: true,
		create: function (input, callback){
			var _this = this;
			$.ajax({
				url: '/tag/ajaxCreateTag/<?php echo $object_class; ?>/<?php echo $id; ?>?tag=' + input,
				type: 'GET',
				success: function (result) {
					if (result) {
						var j_result = JSON.parse(result);
						var option = { id: j_result.id, tag: j_result.tag, type: '<?php echo $object_class; ?>' };
//						
						_this.addOption(option);
						_this.addItem(option.id);
								
						_this.close();
						_this.setTextboxValue('');
						_this.setActiveItem(null);
						_this.setActiveOption(null);
						_this.setCaret(_this.items.length);
						_this.refreshState();

						_this.ignoreFocus = false;
//						
//						try {
//							callback(option);
//						} catch (e) {
//							console.log("Caught Exception", e);
//						}
					}
				}
			});
		},
		onItemAdd: function (tag_id) {
			// var _this = this;
			$.get('/tag/ajaxAddTag/<?php echo $object_class; ?>/<?php echo $id; ?>', {
				_tag_id: tag_id
			});
// 			.done(function(response) {	
// 				if (response) {
// 					var j_result = JSON.parse(response);
// 					var option = { id: j_result.id, tag: j_result.tag, type: '<?php echo $object_class; ?>' };
// //						callback(option);
// 					_this.addOption(option);
// 					_this.addItem(option.id);
// 				}
// 			});
		},
		onItemRemove: function(tag_id) {
			// var _this = this;
			$.ajax({
				url: '/tag/ajaxRemoveTag/<?php echo $object_class; ?>/<?php echo $id; ?>?_tag_id=' + tag_id,
				type: 'GET'
//				success: function (result) {
//					if (result) {
//						var j_result = JSON.parse(result);
//						var option = { id: j_result.id, tag: j_result.tag, type: '<?php echo $object_class; ?>' };
//						callback(option);
//						_this.addOption(option);
//					}
//				}
			});
		}
//		render: {
//			option: function(data, escape) {
//				console.log(data);
//				return escape(data);
//			}
//		}
	});
	
	$(document).ready(function() {
		$('#display_tags_<?php echo $object_class; ?>_<?php echo $id; ?> .selectize-dropdown-content .option.selected').on('click', function(e) {
			debugger;
			// 1. Get the value
			var selectedValue = $(this).attr("data-value");
			// 2. Remove the option
			$('#display_tags_<?php echo $object_class; ?>_<?php echo $id; ?>')[0].selectize.removeItem(selectedValue);
			// 3. Refresh the select
			$('#display_tags_<?php echo $object_class; ?>_<?php echo $id; ?>')[0].selectize.refreshItems();
			$('#display_tags_<?php echo $object_class; ?>_<?php echo $id; ?>')[0].selectize.refreshOptions();
		});
	});
	
	
//	var values = [<?php echo implode(',', array_map(function($tag) {return $tag->id;}, $tags ? : [])); ?>];
//	$select_<?php echo $id; ?>[0].selectize.setValue(values);

</script>