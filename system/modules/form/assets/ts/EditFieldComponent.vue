<script setup>
    import { defineProps, onMounted, ref, watchEffect } from 'vue';
    import MetadataSelect from './MetadataSelect.vue';
    import MetadataSubform from './MetadataSubform.vue';

    // props
    const props = defineProps(['formId', 'fieldId', 'field', 'title', 'types', 'metadata', 'form_list']);
    
    // data
    const should_update_technical_name = ref(false);
    const loading_metadata = ref(false);
    const metadata_form = ref('');
    const selected_type = ref('');
    // const name = ref(props.field.name);
    // const technical_name = ref(props.field.technical_name);
    const field = JSON.parse(props.field);
    const field_name = ref(field.name);
    const field_technical_name = ref(field.technical_name);

    console.log("Field", field)
    const types = ref(JSON.parse(props.types));
    const metadata = ref(JSON.parse(props.metadata));
    const form_list = ref(JSON.parse(props.form_list));

    let vue_metadata_components = ['subform', 'select', 'autocomplete'];

    // watch
    watchEffect(selected_type, () => getMetadataForm());

    // computed
    const metadata_form_html = () => {
        console.log("MF", metadata_form.value);
        const _div = document.createElement('div');
        _div.innerHTML = metadata_form.value;
        return _div.innerHTML;
    };

    // Methods
    const updateTechnicalName = () => {
        if (should_update_technical_name) {
            field_technical_name.value = field_name.value.toLowerCase().replace(/ /g, '_');
        }
    }

    const disableUpdate = () => should_update_technical_name.value = false;
    const selectedTypeIsVueComponent = () => vue_metadata_components.indexOf(selected_type.value) !== -1;

    const getMetadataForm = () => {
        if (!selectedTypeIsVueComponent()) {
            metadata_form.value = '';
            loading_metadata.value = true;

            fetch('/form-field/ajaxGetMetadata/' + props.fieldId + '?type=' + selected_type.value).then(response => response.text()).then(response => {
                console.log("Response", response);
                metadata_form.value = response;
                loading_metadata.value = false;
            })
        }
    }

    // onMounted
    onMounted(() => {
        selected_type.value = field.type;
    });

    // onCreated
    if (props.field.name?.length == 0) {
        should_update_technical_name = true;
    }
</script>
<template>
    <h3 class="pt-2 px-2" v-html="props.title"></h3>
    <form :id="'form_field_edit_' + props.fieldId" class="px-2 pb-2" :action="'/form-field/edit/' + props.fieldId + '?form_id=' + props.formId" method="POST">
        <label class="mt-2" for="name">Name</label>
        <input id="name" name="name" type="text" class="form-control" v-model="field_name" v-on:keyup="updateTechnicalName()" />
        <label class="d-block mt-2" for="technical_name">Key <small class="d-inline">must be unique to the form</small></label>
        <input id="technical_name" name="technical_name" type="text" class="form-control" v-model="field_technical_name" v-on:keyup="disableUpdate()" />
        <label for="type" class="mt-2">Type</label>
        <select id="type" name="type" v-model="selected_type" class="form-select">
            <option v-for="type in types" :value="type[1]">{{ type[0] }}</option>
        </select>
        <div class="additional_details" v-show="!loading_metadata">
            <div v-if='!selectedTypeIsVueComponent()' v-html="metadata_form_html()"></div>
            <metadata-select v-if="selected_type == 'select' || selected_type == 'autocomplete'" :default-value="metadata"></metadata-select>
            <metadata-subform v-if="selected_type == 'subform'" :forms="form_list" :default-value="metadata"></metadata-subform>
        </div>
        <div class="row mt-4">
            <div class="col">
                <button class="btn btn-primary">Save</button>
                <button class="btn btn-secondary form-cancel-button" type="button">Cancel</button>
            </div>
        </div>
    </form>
</template>