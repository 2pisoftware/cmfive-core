<form action='/form-field/edit/<?php echo $field->id; ?>?form_id=<?php echo $form_id; ?>' method="POST">
	<div class="row">
		<div class="large-12 columns">
			<label>Name
				<input type="text" id="name" name="name" placeholder="Name" value="<?php echo $field->name; ?>" />
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

</script>