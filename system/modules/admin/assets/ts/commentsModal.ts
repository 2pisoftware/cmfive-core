import { createApp } from '../../../../templates/base/node_modules/vue';
import CommentsModalComponent from './CommentsModalComponent.vue';

console.log("test")

window.addEventListener("load", () => {
    // @ts-ignore
	window.cmfiveEventBus.addEventListener('modal-load', (event) => {
        createApp({
			components: {
				CommentsModalComponent,
			}
		}).mount("#comments_modal_app");
    });
});