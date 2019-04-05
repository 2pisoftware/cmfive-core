<?php

// if (!empty($form)) {
// 	echo $form;
// }

// ?>

<!-- <script>$("form").submit(function(event) {toggleModalLoading();});</script> -->

<div v-cloak id="app">
	<div class="overlay" v-if="is_loading"></div>
	<div class="panel">
		<h3>New Comment</h3>
		<form id="comment_form" method="POST" @submit.prevent="">
			<div class="small-12 medium-12 large-12">
				<textarea placeholder="Add comment here..." @input="textareaAutoResize" ref="textarea" v-model="comment"></textarea>
			</div><br>
			<div v-if="can_restrict">
				<label class="cmfive__checkbox-container">Restricted
					<input type="checkbox" v-model="is_restricted">
					<span class="cmfive__checkbox-checkmark"></span>
				</label>
				<div v-show="is_restricted"><strong>Select the viewers that can view this attachment</strong>
				</div>
			</div><br>
			<button class="small" style="margin-bottom: 0rem;" @click="saveComment()">Save</button>
		</form>
	</div>
</div>
<script>
	var app = new Vue({
		el: "#app",
		data: function() {
			return {
				comment: null,
				can_restrict: "<?php echo $can_restrict; ?>",
				is_restricted: false,
				is_loading: false
			}
		},
  		methods: {
			textareaAutoResize: function() {
				this.$refs.textarea.style.cssText = 'min-height: 5rem; resize: none;';
    			this.$refs.textarea.style.cssText = 'height:' + (this.$refs.textarea.scrollHeight + 2) + 'px';
			},
			saveComment: function() {

			}
		},
		mounted: function() {
			this.$refs.textarea.style.cssText = 'min-height: 5rem; resize: none;';
		}
	});
</script>
<style>
	div.overlay {
		background: #ffffff5d;
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		z-index: 1000;
	}
</style>