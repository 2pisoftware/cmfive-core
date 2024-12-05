// system/modules/form/assets/ts/editField.ts

import { createApp } from '../../../../templates/base/node_modules/vue/dist/vue.esm-bundler.js';
import EditFieldComponent from './EditFieldComponent.vue';
import testComponent from './testComponent.vue';

class EditFieldBinder {
    static bindInteractions() {
        createApp({
            components: {
                EditFieldComponent,
                testComponent
            },
        }).mount("#edit_field_app");
    }
}

window.addEventListener('load', function() {
    // @ts-ignore
    window.cmfiveEventBus.addEventListener('modal-load', (event) => {
        EditFieldBinder.bindInteractions();
    });
});