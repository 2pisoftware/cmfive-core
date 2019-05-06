<div v-cloak id="app">
	<div class="panel">
		<h3>Edit Attachment</h3>
		<form method="POST" @submit.prevent="">
			<div class="small-12 medium-12 large-12">
				<a v-bind:href="file_directory" target="_blank">{{ file_name }}</a>
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
					<div v-show="is_restricted"><strong>Select the users that can view this attachment</strong>
						<ul class="small-block-grid-1 medium-block-grid-3 large-block-grid-3">
							<li v-for="viewer in viewers" style="padding-bottom: 0;">
								<label class="cmfive__checkbox-container" v-if="viewer.id != <?php echo $w->Auth->user()->id; ?>">{{ viewer.name }}
									<input type="checkbox" v-model="viewer.can_view">
									<span class="cmfive__checkbox-checkmark" ></span>
								</label>
							</li>
						</ul>
						<strong>Attachment Owner</strong>
						<select @change="updateOwner">
							<option v-for="viewer in canViewViewers" :value="JSON.stringify(viewer)">
								{{ viewer.name }}
							</option>
						</select>
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
				id: "<?php echo $id; ?>",
				can_restrict: "<?php echo $can_restrict; ?>",
				viewers: <?php echo empty($viewers) ? json_encode([]) : $viewers; ?>,
				new_owner: <?php echo empty($new_owner) ? json_decode([]) : $new_owner; ?>,
				title: "<?php echo $title; ?>",
				description: "<?php echo $description; ?>",
				file_name: "<?php echo $file_name; ?>",
				file_directory: "<?php echo $file_directory; ?>",
				file: null,
				is_restricted: ("<?php echo $is_restricted; ?>" == "true"),
				max_upload_size: "<?php echo @$w->File->getMaxFileUploadSize() ? : (2 * 1024 * 1024); ?>",
				redirect_url: "<?php echo $redirect_url; ?>",
			}
		},
		methods: {
			prepareFile: function() {
				this.file = this.$refs.file.files[0];
			},
			updateOwner: function(event) {
				this.new_owner = JSON.parse(event.target.value);
			},
			uploadFile: function() {
				if (this.file != null && this.file.size > this.max_upload_size) {
					new Toast("File size is too large").show();
					return;
				}

				toggleModalLoading();

				var file_data = {
					id: this.id,
					title: this.title,
					description: this.description,
					is_restricted: this.is_restricted,
					new_owner: this.new_owner,
					viewers: this.viewers.filter(function(viewer) {
						return viewer.can_view;
					})
				};

				var formData = new FormData();
				formData.append("file", this.file);
				formData.append("file_data", JSON.stringify(file_data));

				axios.post("/file-attachment/ajaxEditAttachment",
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

				});
			}
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