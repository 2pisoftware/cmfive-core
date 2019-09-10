/**
 * Select metadata form
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
Vue.component('metadata-select', {
	props: {
		defaultValue: {
			required: true,
			type: Array
		}
	},
	data: function() {
		return {
			is_object_map: 1,
			user_rows: []
		}
	},
	methods: {
		showObjectMap: function() {
			return this.is_object_map == "1";
		},
		getDefaultValue: function(key, default_return) {
			if (this.defaultValue !== undefined) {
				for(var i in this.defaultValue) {
					if (this.defaultValue[i].meta_key == key) {
						return this.defaultValue[i].meta_value;
					}
				}
			}

			return default_return;
		},
		addRow: function() {
			this.user_rows.push({key: '', value: ''});
		},
		getRowFieldName: function(type, index) {
			return "user_rows[" + index + "][" + type + "]";
		},
		removeRow: function(index) {
			this.user_rows.splice(index, 1);
		}
	},
	template:   '<div class="row small-12 columns"><div class="vue-metadata-select__container"> \
					<label>Additional Details</label>\
					<div class="row-fluid clearfix vue-metadata-select__radio-header"> \
						<div class="small-6 columns"> \
							<label><input type="radio" name="is_object_map" value="1" v-model="is_object_map" /> \
								Object Map \
							</label> \
						</div><div class="small-6 columns"> \
							<label><input type="radio" name="is_object_map" value="0" v-model="is_object_map" /> \
								User defined options \
							</label> \
						</div> \
					</div> \
					<div class="row-fluid clearfix vue-metadata-select__content-container" v-if="showObjectMap()"> \
						<div class="row-fluid"><div class="small-12"><label class="small-12 columns">Object \
							<input type="text" name="object_type" :value="getDefaultValue(\'object_type\', \'\')" id="vue-metadata-select__object-type" /> \
						</label></div></div>\
						<div class="row-fluid"><div class="small-12"><label class="small-12 columns">Filter \
							<input type="text" name="object_filter" :value="getDefaultValue(\'object_filter\', \'\')" id="vue-metadata-select__object-filter" /> \
						</label></div></div> \
						<div class="row-fluid"><div class="small-12"><label class="small-12 columns">Options \
							<input type="text" name="options" :value="getDefaultValue(\'options\', \'\')" id="vue-metadata-select__options" /> \
						</label></div></div> \
					</div> \
					<div class="row-fluid clearfix vue-metadata-select__content-container" v-if="!showObjectMap()"> \
						<div class="row-fluid small-12"> \
							<button type="button" class="button tiny info" @click="addRow()">Add row</button> \
						</div> \
						<div class="row-fluid" v-for="(row, index) in user_rows"> \
							<div class="small-12 medium-5 columns">\
								<label>Key <input type="text" :name="getRowFieldName(\'key\', index)" v-model="user_rows[index].key" /></label> \
							</div> \
							<div class="small-12 medium-5 columns"> \
								<label>Value <input type="text" :name="getRowFieldName(\'value\', index)" v-model="user_rows[index].value" /></label> \
							</div> \
							<div class="small-12 medium-2 columns"> \
								<button type="button" class="button tiny alert vue-metadata-select__button" @click="removeRow(index)">Delete</button>\
							</div> \
						</div> \
					</div> \
				</div></div>',
	created: function() {
		this.is_object_map = this.getDefaultValue('is_object_map', 1);
		this.user_rows = this.getDefaultValue('user_rows', [])
	}
});