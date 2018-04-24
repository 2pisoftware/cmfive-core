/**
 * A vue component to render a Foundation row, with label and content inside
 * This works best when used inside a form, though it's not necessary
 *
 * NOTE: Currently doesn't support grid usage for putting multiple elements in a single row
 * Usage:
 *     <form-row label="[label is required]"> [form field/content goes here] </form-row>
 *
 * @author  Adam Buckley <adam@2pisoftware.com>
 */

Vue.component('form-row', {
	props: {
		label: String,
		labelFor: String
	},
	template:  '<div class="row"> \
					<div class="large-12 columns"> \
						<label v-if="label" v-bind:for="labelFor">{{ label }}<slot></slot></label> \
						<slot v-else></slot> \
					</div> \
				</div>'
});