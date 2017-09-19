/**
 * Shows loading indicator from Spinkit (http://tobiasahlin.com/spinkit/), CSS file needed.
 * "show" property controls visibility.
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
Vue.component('loading-indicator', {
	props: {
		show: {
			type: Boolean,
			required: true
		}
	},
	template: '<div class="spinner" v-show="show"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>'
});
