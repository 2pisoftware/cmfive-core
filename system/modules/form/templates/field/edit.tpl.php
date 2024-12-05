<!-- <link rel='stylesheet' href='/system/templates/vue-components/loading-indicator.vue.css' /> -->
<div id="edit_field_app">
    <!-- <test-component test="world"></test-component> -->
    <edit-field-component
        form-id='<?php echo $form_id; ?>'
        field-id='<?php echo $field->id; ?>'
        field='<?php echo json_encode($field->toArray()); ?>'
        title='<?php echo $title; ?>'
        types='<?php echo json_encode(FormField::getFieldTypes()); ?>'
        metadata='<?php echo json_encode(array_map(fn ($metadata) => $metadata->toArray(), $field->getMetadata() ?: [])); ?>'
        form_list='<?php echo json_encode(array_map(fn ($form) => $form->toArray(), FormService::getInstance($w)->getForms() ?: [])); ?>'>
    </edit-field-component>
</div>
