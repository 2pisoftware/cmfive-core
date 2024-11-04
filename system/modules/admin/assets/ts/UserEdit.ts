import { createApp } from '../../../../templates/base/node_modules/vue';
import UserEditComponent from "./UserEditComponent.vue";

window.addEventListener("load", () => {
	createApp({
		components: {
			UserEditComponent,
		}
	}).mount("#user_edit_app");
})