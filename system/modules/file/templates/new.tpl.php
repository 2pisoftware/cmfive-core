<div v-cloak id="app">
	<div class="overlay" v-if="is_loading"></div>
	<div class="panel">
		<h3>New Attachment</h3>
		<form method="POST" @submit.prevent="">
			<div class="small-12 medium-12 large-12">
				<label>
					<input type="file" id="file" ref="file" @change="prepareFile()"/>
				</label>
				<label style="font-size: 18px;">Title
					<input type="text" id="title" v-model="title"/>
				</label>
				<label style="font-size: 18px;">Description
					<input type="text" id="description" v-model="description"/>
				</label><br>
				<div v-if="can_restrict">
					<label class="cmfive__checkbox-container">Restricted
						<input type="checkbox" v-model="is_restricted">
						<span class="cmfive__checkbox-checkmark"></span>
					</label>
					<div v-show="is_restricted"><strong>Select the viewers that can view this attachment</strong>
						<div v-for="viewer in viewers" class="small-12 medium-6 large-4">
							<label class="cmfive__checkbox-container">{{ viewer.firstname + " " + viewer.lastname }}
								<input type="checkbox" v-model="viewer.can_view">
								<span class="cmfive__checkbox-checkmark" ></span>
							</label>
						</div>
					</div>
				</div>
			</div><br>
			<button class="small" style="margin-bottom: 0rem;" @click="uploadFile()">Save</button>
		</form>
	</div>
</div>
<script>
	var app = new Vue({
		el: "#app",
		data: function() {
			return {
				can_restrict: "<?php echo $can_restrict; ?>",
				viewers: <?php echo $viewers; ?>,
				title: null,
				description: null,
				file: null,
				is_restricted: false,
				max_upload_size: "<?php echo @$w->File->getMaxFileUploadSize() ? : (2 * 1024 * 1024); ?>",
				class: "<?php echo $class; ?>",
				class_id: "<?php echo $class_id; ?>",
				redirect_url: "<?php echo $redirect_url; ?>",
				is_loading: false
			}
		},
		methods: {
			prepareFile: function() {
				this.file = this.$refs.file.files[0];
			},
			uploadFile: function() {
				if (this.file === null) {
					new Toast("No file selected").show();
					return;
				}

				if (this.file.size > this.max_upload_size) {
					new Toast("File size is too large").show();
					return;
				}

				this.is_loading = true;

				var file_data = {
					title: this.title,
					description: this.description,
					class: this.class,
					class_id: this.class_id,
					is_restricted: this.is_restricted,
					viewers: this.viewers.filter(function(viewer) {
						return viewer.can_view;
					})
				};

				var formData = new FormData();
				formData.append("file", this.file);
				formData.append("file_data", JSON.stringify(file_data));

				axios.post("/file-attachment/ajaxAddAttachment",
					formData, {
						headers: {
							"Content-Type": "multipart/form-data"
						}
					}
				).then(function(response) {
					window.history.go();
				}).catch(function(error) {
					new Toast("Failed to upload file").show();
					console.log(error);
				}).finally(function() {
					app.is_loading = false;
				});
			}
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