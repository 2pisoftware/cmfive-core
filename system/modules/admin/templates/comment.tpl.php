<div v-cloak id="app">
	<div class="panel">
		<h3>{{ is_new_comment == "true" ? "New Comment" : "Edit Comment" }}</h3>
		<form id="comment_form" method="POST" @submit.prevent="">
			<div>
				<textarea placeholder="Add comment here..." @input="textareaAutoResize" ref="textarea" v-model="comment"></textarea><br>
				<div v-if="viewers.length !== 0">
					<strong>Select the users that will be notified by this comment</strong>
				</div>
				<div v-for="viewer in viewers">
					<label class="cmfive__checkbox-container">{{ viewer.name }}
						<input type="checkbox" v-model="viewer.is_notify" @click="toggleIsNotify(viewer)">
						<span class="cmfive__checkbox-checkmark"></span>
					</label>
				</div>
			</div><br>
			<div v-if="can_restrict == 'true'">
				<label class="cmfive__checkbox-container">Restricted
					<input type="checkbox" v-model="is_restricted" @click="toggleIsRestricted()" :disabled="is_parent_restricted">
					<span class="cmfive__checkbox-checkmark"></span>
				</label>
				<div v-show="is_restricted"><strong>Select the users that can view this comment</strong>
					<div v-for="viewer in viewers" class="small-12 medium-6 large-4">
						<label class="cmfive__checkbox-container" v-if="viewer.id != <?php echo $w->Auth->user()->id; ?>">{{ viewer.name }}
							<input type="checkbox" v-model="viewer.can_view" @click="toggleCanView(viewer)">
							<span class="cmfive__checkbox-checkmark" ></span>
						</label>
					</div>
					<strong v-if="comment_id != 0">Comment Owner</strong>
					<select v-if="comment_id != 0" @change="updateOwner">
						<option v-for="viewer in canViewViewers" :value="JSON.stringify(viewer)">
							{{ viewer.name }}
						</option>
					</select>
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
				comment: "<?php echo $comment; ?>",
				comment_id: "<?php echo $comment_id; ?>",
				viewers: <?php echo empty($viewers) ? json_encode([]) : $viewers; ?>,
				top_object_table_name: "<?php echo $top_object_table_name; ?>",
				top_object_id: "<?php echo $top_object_id; ?>",
				new_owner: <?php echo empty($new_owner) ? json_decode([]) : $new_owner; ?>,
				can_restrict: "<?php echo $can_restrict; ?>",
				is_new_comment: "<?php echo $is_new_comment; ?>",
				is_internal_only: "<?php echo $is_internal_only; ?>",
				is_restricted: <?php echo $is_restricted; ?>,
				is_parent_restricted: <?php echo $is_parent_restricted; ?>,
			}
		},
  		methods: {
			toggleIsRestricted: function() {
				this.is_restricted = !this.is_restricted;

				if (this.is_restricted) {
					this.viewers.forEach(function(viewer, index) {
						if (!viewer.can_view) {
							app.viewers[index].is_notify = false;
						}
					});
					return;
				}

				this.viewers.forEach(function(viewer, index) {
					app.viewers[index].is_notify = viewer.is_original_notify;
				});
			},
			toggleCanView: function(viewer) {
				viewer.can_view = !viewer.can_view;

				if (viewer.can_view && viewer.is_original_notify) {
					viewer.is_notify = true;
				}

				if (!viewer.can_view) {
					viewer.is_notify = false;
				}
			},
			toggleIsNotify: function(viewer) {
				viewer.is_notify = !viewer.is_notify;

				if (viewer.is_notify && this.is_restricted) {
					viewer.can_view = true;
				}
			},
			textareaAutoResize: function() {
				this.$refs.textarea.style.cssText = 'min-height: 5rem; resize: none;';
    			this.$refs.textarea.style.cssText = 'height:' + (this.$refs.textarea.scrollHeight + 2) + 'px';
			},
			updateOwner: function(event) {
				this.new_owner = JSON.parse(event.target.value);
			},
			saveComment: function() {
				if (this.comment === null || this.comment.trim() === "") {
					new Toast("Comment cannot be blank").show();
					return;
				}

				toggleModalLoading();

				axios.post("/admin/ajaxAddComment", {
					comment: app.comment,
					comment_id: app.comment_id,
					viewers: app.viewers,
					new_owner: app.new_owner,
					top_object_table_name: app.top_object_table_name,
					top_object_id: app.top_object_id,
					is_internal_only: app.is_internal_only,
					is_restricted: app.is_restricted
				}).then(function(response) {
					window.history.go();
				}).catch(function(error) {
					new Toast("Failed to save comment").show();
					console.log(error);
				}).finally(function() {

				})
			}
		},
		mounted: function() {
			this.$refs.textarea.style.cssText = 'min-height: 5rem; resize: none;';
		},
		computed: {
			canViewViewers: function() {
				return this.viewers.filter(function(viewer) {
					return viewer.can_view;
				});
			}
		}
	});
</script>