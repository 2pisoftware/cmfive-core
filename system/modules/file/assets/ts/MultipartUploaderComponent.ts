import { createApp } from 'vue';

import MultipartUploaderComponent from "./MultipartUploaderComponent.vue";

window.addEventListener("load", () => {
    createApp({
        components: {
            MultipartUploaderComponent,
        }
    }).mount("#multipart-uploader");
})