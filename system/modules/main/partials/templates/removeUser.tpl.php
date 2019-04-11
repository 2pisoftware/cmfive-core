<div v-cloak id="app">
	<div v-show="!new_owner_set && owner_links.length > 0">
		<h3>Main</h3>
		<p>This user has ownership of {{ owner_links.length }} object(s). Select a new user to take over ownership.</p>
		<form method="POST" @submit.prevent="">
			<select @change="updateSelectedOwner">
				<option v-for="user in users" :value="JSON.stringify(user)">
					{{ user.name }}
				</option>
			</select>
			<br><br><button class="small warning" @click="setNewOwner">Save</button>
		</form>
	</div>
</div>
<script>
	var app = new Vue({
		el: "#app",
		data: function() {
			return {
				deleting_user_id: "<?php echo $deleting_user_id; ?>",
				users: <?php echo empty($users) ? json_encode([]) : $users; ?>,
				owner_links: <?php echo empty($owner_links) ? json_encode([]) : $owner_links; ?>,
				new_owner: null,
				new_owner_set: false,
			}
		},
		methods: {
			updateSelectedOwner: function(event) {
				this.new_owner = JSON.parse(event.target.value);
			},
			setNewOwner: function() {
				if (!confirm("Are you sure you want to set this user as the new owner of these object(s)?")) {
					return;
				}

				toggleModalLoading();

				axios.post("/main/ajaxSetNewOwner", {
					deleting_user_id: app.deleting_user_id,
					new_owner_id: app.new_owner.id,
					owner_links: app.owner_links,
				}).then(function(response) {
					app.new_owner_set = true;
					new Toast("New owner successfully set").show();
				}).catch(function(error) {
					new Toast("Failed to set new owner").show();
					console.log(error);
				}).finally(function() {
					toggleModalLoading();
				});
			}
		},
		mounted: function() {
			this.$nextTick(function() {
				this.new_owner = this.users[0];
			})
		}
	});
</script>