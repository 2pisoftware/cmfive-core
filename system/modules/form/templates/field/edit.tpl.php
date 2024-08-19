<!-- <link rel='stylesheet' href='/system/templates/vue-components/loading-indicator.vue.css' /> -->

<h3 class="pt-2 px-2"><?php echo $title; ?></h3>
<form id="form_field_edit_<?php echo $field->id; ?>" class="px-2 pb-2" action='/form-field/edit/<?php echo $field->id; ?>?form_id=<?php echo $form_id; ?>' method="POST">
    <label class="mt-2" for="name">Name</label>
    <?php echo new \Html\Form\InputField([
        'id|name' => "name",
        'placeholder' => 'Name',
        'value' => $field->name,
        'v-model' => 'name',
        'v-on:keyup' => 'updateTechnicalName()',
        'class' => 'form-control',
    ]); ?>
    <label class="d-block mt-2" for="technical_name">Key <small class="d-inline">must be unique to the form</small></label>
    <?php echo new \Html\Form\InputField([
        'id|name' => 'techincal_name',
        'placeholder' => 'Key (unique identifier)',
        'value' => $field->technical_name,
        'v-model' => 'technical_name',
        'v-on:keyup' => 'disableUpdate()',
        'class' => 'form-control',
    ]); ?>
    <label for="type" class="mt-2">Type</label>
    <select id="type" name="type" v-model="selected_type" class="form-select">
        <option v-for="type in types" :value="type[1]">{{ type[0] }}</option>
    </select>
    <div class="additional_details" v-show="!loading_metadata">
        <div v-if='!selectedTypeIsVueComponent()' v-html="metadata_form_html"></div>
        <?php
        echo VueComponentRegister::getComponent('metadata-select')->display([
            "v-if" => 'selected_type == "select" || selected_type == "autocomplete"', ':default-value' => 'metadata'
        ]);
        echo VueComponentRegister::getComponent('metadata-subform')->display([
            "v-if" => 'selected_type == "subform"', ':forms' => 'form_list', ':default-value' => 'metadata'
        ]);
        ?>
    </div>
    <!-- <loading-indicator :show="loading_metadata"></loading-indicator> -->
    <div class="row mt-4">
        <div class="col">
            <button class="btn btn-primary">Save</button>
            <button class="btn btn-secondary" type="button" onclick="if($('#cmfive-modal').is(':visible')){ $('#cmfive-modal').foundation('reveal', 'close'); } else { window.history.back(); }">Cancel</button>
        </div>
    </div>
</form>
<!-- <script src='/system/templates/vue-components/loading-indicator.vue.js'></script> -->
<script>
    const {
        createApp
    } = Vue;
    createApp({
        setup() {
            return {
                vue_metadata_components: ['subform', 'select', 'autocomplete'],
                should_update_technical_name: false,
                name: '<?php echo $field->name; ?>',
                technical_name: '<?php echo $field->technical_name; ?>',
                selected_type: '<?php echo $field->type; ?>',
                types: <?php echo json_encode(FormField::getFieldTypes()); ?>,
                loading_metadata: false,
                metadata: <?php echo json_encode(array_map(fn ($metadata) => $metadata->toArray(), $field->getMetadata() ?: [])); ?>,
                form_list: <?php echo json_encode(array_map(fn ($form) => $form->toArray(), FormService::getInstance($w)->getForms() ?: [])); ?>,
                metadata_form: ''
            }
        },
        computed: {
            metadata_form_html: function() {
                const _div = document.createElement('div');
                _div.innerHTML = this.metadata_form;
                return _div.textContent || _div.innerText || '';
                // return $('<div/>').html(this.metadata_form).text();
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
            getMetadataForm: async function() {
                if (!this.selectedTypeIsVueComponent()) {
                    this.metadata_form = '';
                    this.loading_metadata = true;

                    const response = await fetch('/form-field/ajaxGetMetadata/<?php echo $field->id; ?>?type=' + this.selected_type)
                    const text_response = await response.text();

                    this.metadata_form = text_response;
                    this.loading_metadata = false;
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
    }).mount("#form_field_edit_<?php echo $field->id; ?>");
</script>