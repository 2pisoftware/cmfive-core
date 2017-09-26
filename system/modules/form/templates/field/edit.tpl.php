<h3><?php echo $title; ?></h3>
<form id="form_edit_<?php echo $field->id; ?>" action='/form-field/edit/<?php echo $field->id; ?>?form_id=<?php echo $form_id; ?>' method="POST">
	<div class="row">
		<div class="large-12 columns">
			<label>Name
				<input type="text" id="name" name="name" placeholder="Name" value="<?php echo $field->name; ?>" v-model="name" v-on:keyup="updateTechnicalName()"/>
			</label>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<label>Key <small>must be unique to the form</small>
				<input type="text" id="technical_name" name="technical_name" placeholder="Key (unique identifier)" value="<?php echo $field->technical_name; ?>" v-model="technical_name" v-on:keyup="disableUpdate()" />
			</label>
		</div>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<label>Type
				<select id="type" name="type">
					<?php $types = FormField::getFieldTypes();
						if (!empty($types)) :
							foreach($types as $type) : ?>
								<option value="<?php echo $type[1]; ?>" <?php echo ($type[1] == $field->type) ? "selected='selected'" : ""; ?>><?php echo $type[0]; ?></option>
							<?php endforeach;
						endif;
					?>
				</select>
			</label>
		</div>
	</div>
	<div class="row additional_details">
		<?php 
			if (!empty($metadata_form)) {
				echo Html::form($metadata_form);
			} 
		?>
	</div>
	<div class="row">
		<div class="large-12 columns">
			<button class="button">Save</button>
			<button class="button secondary" type="button" onclick="if($('#cmfive-modal').is(':visible')){ $('#cmfive-modal').foundation('reveal', 'close'); } else { window.history.back(); }">Cancel</button>
		</div>
	</div>
</form>
<script>
	
	$("select[name='type']").change(function (event) {
		var _this = $(this);
		$(".additional_details").empty();
		$.get("/form-field/ajaxGetMetadata/<?php echo $field->id; ?>?type=" + $("option:selected", _this).val(), function (response) {
			if (response.length) {
				$(".additional_details").append(response);
			}
		});
	});


	// var should_update_technical_name = <?php echo empty($field->technical_name) ? 'true' : 'false'; ?>;
	// $("#name").keyup(function(event) {
	// 	if (should_update_technical_name) {
	// 		$("#technical_name").val($("#name").val().toLowerCase().replace(/ /g, '_'));
	// 	}
	// });

	// $("#technical_name").keyup(function(event) {
	// 	should_update_technical_name = false;
	// });

	var form_edit_<?php echo $field->id; ?>_vm = new Vue({
		el: "#form_edit_<?php echo $field->id; ?>",
		data: {
			should_update_technical_name: false,
			name: '<?php echo $field->name; ?>',
			technical_name: '<?php echo $field->technical_name; ?>'
		},
		methods: {
			updateTechnicalName: function() {
				if (this.should_update_technical_name) {
					this.technical_name = this.name.toLowerCase().replace(/ /g, '_');
				}
			},
			disableUpdate: function() {
				this.should_update_technical_name = false;
			}
		},
		created: function() {
			if (this.name.length == 0) {
				this.should_update_technical_name = true;
			}
		}
	});

</script>