import { createApp } from '../../../../templates/base/node_modules/vue';
import UserSecurityComponent from "./UserSecurityComponent.vue";

window.addEventListener("load", () => {
	createApp({
		components: {
			UserSecurityComponent,
		}
	}).mount("#user_security_app");
})