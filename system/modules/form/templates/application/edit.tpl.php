<style>
	#form-application-<?php echo $application->id; ?>__vue-instance tr > td > a {
		margin-bottom: 0px;
	}
</style>
<div id='form-application-<?php echo $application->id; ?>__vue-instance' style='background-color: #efefef; padding-top: 20px;'>
	<div class='row'>
		<div class='small-12 columns'>
			<h3>Editing Application: {{ application.title }}</h3>
		</div>
	</div>
	<form v-on:submit='saveApplication()'>
		<form-row label="Title">
			<input type="text" v-model='application.title' v-on:keyup.prevent='form_changed = true' v-bind:value="application.title" />
		</form-row>
		<form-row label="Description">
			<textarea v-model='application.description' v-on:keyup.prevent='form_changed = true'>{{ application.description }}</textarea>
		</form-row>
		<form-row label="Active" labelfor="active_switch">
			<div class="switch">
				<input id="active_switch" name='is_active' type="checkbox" v-on:change='form_changed = true' v-bind:checked='setChecked()' v-model='application.is_active'>
				<label for='active_switch'></label>
			</div>
		</form-row>
		<form-row v-show='form_changed'>
			<a v-on:click='saveApplication()' class='button small'>Save</a>
			<a v-on:click='resetApplication()' class='button small secondary right'>Cancel</a>
		</form-row>
	</form>

	<hr/>

	<div class='row-fluid'>
		<div class='small-12 medium-6 columns'>
			<h3>Members <button class='button tiny right' v-on:click='editApplicationMember()'>Add member</button></h3>
			<loading-indicator :show="loading_members"></loading-indicator>
			<table style='width: 100%;' v-show='!loading_members'>
				<thead><tr><th>User</th><th>Role</th><th>Actions</th></tr></thead>
				<tbody>
					<tr v-if='application_members.length' v-for='member in application_members'>
						<td>{{ member.name }}</td>
						<td>{{ member.role }}</td>
						<td>
							<a class='button tiny' v-on:click='editApplicationMember(member)'>Edit</a>
							<a class='button tiny warning' v-on:click='deleteApplicationMember(member)'>Delete</a>
						</td>
					</tr>
					<tr v-if='!application_members.length'><td colspan="3">No members found</td></tr>
				</tbody>
			</table>
		</div>
		<div class='small-12 medium-6 columns'>
			<h3>Attached Forms <button class='button tiny right' v-on:click='editApplicationForm()'>Attach form</button></h3>
			<table style='width: 100%;' v-show='!loading_forms'>
				<thead><tr><th>Form</th><th># saved rows</th><th>Actions</th></tr></thead>
				<tbody>
					<tr v-if='application_forms.length' v-for='form in application_forms'>
						<td>{{ form.name }}</td>
						<td>{{ form.role }}</td>
						<td>
							<a class='button tiny' v-on:click='editApplicationForm(form)'>Edit</a>
							<a class='button tiny warning' v-on:click='deleteApplicationForm(form)'>Delete</a>
						</td>
					</tr>
					<tr v-if='!application_forms.length'><td colspan="3">No forms found</td></tr>
				</tbody>
			</table>
		</div>
	</div>

	<!-- Form modal -->
	<modal id="form_application_form_modal" modalTitle="Add Form">

	</modal>

	<!-- Member modal -->
	<div id="form_application_member_modal" class="reveal-modal" data-reveal aria-labelledby="member_modalTitle" aria-hidden="true" role="dialog">
		<h2 id="member_modalTitle">{{ active_member.id != undefined || active_member.id != null ? 'Edit member' : 'Create member' }}</h2>
		<form v-on:submit='saveMember()'>
			<form-row label="User">
				<autocomplete :list="user_list" v-on:autocomplete-select="setSelectedValue" property="name" :required="true" :threshold="1"></autocomplete>
			</form-row>
			<form-row label="Role">
				<select v-model="active_member.role">
					<option v-for="option in member_role_options" v-bind:value="option">
						{{ option }}
					</option>
				</select>
			</form-row>
			<br/>
			<form-row>
				<button v-on:click.prevent='saveApplicationMember()' class='button tiny'>Save</button>
				<button v-on:click.prevent='resetActiveMember()' class='button tiny secondary right'>Cancel</button>
			</form-row>
		</form>
		<a class="close-reveal-modal" aria-label="Close">&#215;</a>
	</div>
</div>

<script src='/system/templates/vue-components/modal.vue.js'></script>

<link rel='stylesheet' href='/system/templates/vue-components/form/elements/autocomplete.vue.css' />
<script src='/system/templates/vue-components/form/elements/autocomplete.vue.js'></script>

<link rel='stylesheet' href='/system/templates/vue-components/loading-indicator.vue.css' />
<script src='/system/templates/vue-components/loading-indicator.vue.js'></script>

<script src='/system/templates/vue-components/form/form-row.vue.js'></script>
<script>

	var form_application_vue_instance = new Vue({
		el: '#form-application-<?php echo $application->id; ?>__vue-instance',
		data: {
			shadow_application: <?php echo json_encode($application->toArray(), JSON_FORCE_OBJECT); ?>,
			application: <?php echo json_encode($application->toArray(), JSON_FORCE_OBJECT); ?>,
			form_changed: false,
			application_members: {},
			application_forms: {},
			loading_application: false,
			loading_members: true,
			loading_forms: true,


			member_role_options: <?php echo json_encode(FormApplicationMember::$_roles, true); ?>,
			active_member: {
				member_user_id: null,
				role: '',
				application_id: '<?php echo $application->id; ?>'
			},
			active_form: {},
			user_list: <?php echo json_encode(array_map(function($user) {return ['id' => $user->id, 'name' => $user->getFullName()];}, array_filter($w->Auth->getUsers(), function($user) {return !empty($user->id) && $user->is_active == 1 && $user->is_deleted == 0;})), true); ?>
		},
		methods: {
			setSelectedValue: function(selectedValue) {
				this.active_member.member_user_id = selectedValue;
			},
			setChecked: function() {
				if (this.application.is_active !== undefined && this.application.is_active == 1) {
					return true;
				}
				return false;
			},
			saveApplication: function() {
				var _this = this;
				$.ajax('/form-vue/save_application/<?php echo $application->id; ?>', {
					method: 'POST',
					data: _this.application
				}).done(function(response) {
					var _response = JSON.parse(response);
					if (_response.success === true) {
						_this.shadow_application = Vue.util.extend({}, _this.application);
					}
					_this.form_changed = false;
				});
			},
			resetApplication: function() {
				this.application = Vue.util.extend({}, this.shadow_application);
				this.form_changed = false;
			},
			getApplicationForms: function() {
				this.loading_forms = false;
			},
			getApplicationMembers: function() {
				var _this = this;
				this.loading_members = true;
				$.ajax('/form-vue/get_members/' + this.application.id).done(function(response) {
					var _response = JSON.parse(response);
					if (_response.success) {
						_this.application_members = _response.data;
					} else {
						alert(_response.error);
					}

					_this.loading_members = false;
				});
			},
			editApplicationForm: function() {
				
			},
			deleteApplicationForm: function() {

			},
			editApplicationMember: function(member_index) {
				if (member_index && ((this.application_members.length - 1) >= member_index)) {
					this.active_member = Vue.utils.extend({}, this.application_members[member_index]);
				}
				$('#form_application_member_modal').foundation('reveal', 'open');
			},
			saveApplicationMember: function() {
				if (this.active_member.member_user_id != undefined) {
					var _this = this;
					$.ajax('/form-vue/save_member', {
						method: 'POST',
						data: _this.active_member
					}).done(function(response) {
						_this.getApplicationMembers();
						_this.resetActiveMember();
					});
				}
			},
			resetActiveMember: function() {
				this.active_member = {
					member_user_id: null,
					role: '',
					application_id: '<?php echo $application->id; ?>'
				};

				$('#form_application_member_modal').foundation('reveal', 'close');
			},
			deleteApplicationMember: function() {

			}
		},
		created: function() {
			this.getApplicationMembers();
			this.getApplicationForms();
		}
	});

</script>