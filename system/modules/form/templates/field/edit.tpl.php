<link rel='stylesheet' href='/system/templates/vue-components/loading-indicator.vue.css' />

<h3><?php echo $title; ?></h3>
<form id="form_field_edit_<?php echo $field->id; ?>" action='/form-field/edit/<?php echo $field->id; ?>?form_id=<?php echo $form_id; ?>' method="POST">
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
				<select id="type" name="type" v-model="selected_type">
					<option v-for="type in types" :value="type[1]">{{ type[0] }}</option>
				</select>
			</label>
		</div>
	</div>
	<div class="additional_details" v-show="!loading_metadata">
		<!-- <div class='large-12 columns'> -->
			<div v-if='!selectedTypeIsVueComponent()' v-html="metadata_form_html"></div>
			<!-- <metadata-autocomplete v-if='selected_type == "autocomplete"'></metadata-autocomplete> -->
			<?php 
				echo VueComponentRegister::getComponent('metadata-select')->display([
					"v-if" => 'selected_type == "select"', ':default-value' => 'metadata'
				]);
				echo VueComponentRegister::getComponent('metadata-subform')->display([
					"v-if" => 'selected_type == "subform"', ':forms' => 'form_list', ':default-value' => 'metadata'
				]); 
			?>
			<!-- <metadata-subform v-if='selected_type == "subform"' :forms="form_list" :default-value="metadata"></metadata-subform> -->
		<!-- </div> -->
	</div>
	<loading-indicator :show="loading_metadata"></loading-indicator>
	<div class="row">
		<div class="large-12 columns">
			<button class="button">Save</button>
			<button class="button secondary" type="button" onclick="if($('#cmfive-modal').is(':visible')){ $('#cmfive-modal').foundation('reveal', 'close'); } else { window.history.back(); }">Cancel</button>
		</div>
	</div>
</form>
<script src='/system/templates/vue-components/loading-indicator.vue.js'></script>
<script>

	var form_field_edit_<?php echo $field->id; ?>_vm = new Vue({
		el: "#form_field_edit_<?php echo $field->id; ?>",
		data: {
			vue_metadata_components: ['subform', 'select', 'autocomplete'],
			should_update_technical_name: false,
			name: '<?php echo $field->name; ?>',
			technical_name: '<?php echo $field->technical_name; ?>',
			selected_type: '<?php echo $field->type; ?>',
			types: <?php echo json_encode(FormField::getFieldTypes()); ?>,
			loading_metadata: false,
			metadata: <?php echo json_encode(array_map(function($metadata) {return $metadata->toArray();}, $field->getMetadata() ? : [])); ?>,
			form_list: <?php echo json_encode(array_map(function($form) {return $form->toArray();}, $w->Form->getForms() ? : [])); ?>,
			metadata_form: ''
		},
		computed: {
			metadata_form_html: function() {
				return $('<div/>').html(this.metadata_form).text();
			}
		},
		watch: {
			selected_type: function() {
				this.getMetadataForm();
			}
		},
		methods: {
			updateTechnicalName: function() {
				if (this.should_update_technical_name) {
					this.technical_name = this.name.toLowerCase().replace(/ /g, '_');
				}
			},
			disableUpdate: function() {
				this.should_update_technical_name = false;
			},
			getMetadataForm: function() {
				if (!this.selectedTypeIsVueComponent()) {
					var _this = this;
					this.metadata_form = '';
					this.loading_metadata = true;
					$.get('/form-field/ajaxGetMetadata/<?php echo $field->id; ?>?type=' + this.selected_type).success(function(response) {
						_this.metadata_form = response;
						_this.loading_metadata = false;
						// _this.$compile(_this.metadata_form_html);
					});
				}
			},
			selectedTypeIsVueComponent: function() {
				return this.vue_metadata_components.indexOf(this.selected_type) > -1;
			}
		},
		created: function() {
			if (this.name.length == 0) {
				this.should_update_technical_name = true;
			}

			if (!this.selectedTypeIsVueComponent()) {
				this.getMetadataForm();
			}
		}
	});

</script>