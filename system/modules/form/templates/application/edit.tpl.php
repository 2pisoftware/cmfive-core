
<div id='form-application-<?php echo $application->id; ?>__vue-instance' style='background-color: #efefef; padding-top: 20px;'>
	<div class='row'>
		<div class='small-12 columns'>
			<h3>Editing Application: {{ application.title }}</h3>
		</div>
	</div>
	<form v-on:submit='saveApplication()'>
		<div class="row">
			<div class="large-12 columns">
				<label>Title
					<input type="text" v-model='application.title' v-on:change='form_changed = true' v-bind:value="application.title" />
				</label>
			</div>
		</div>
		<div class="row">
			<div class="large-12 columns">
				<label>Description
					<textarea v-model='application.description' v-on:change='form_changed = true'>{{ application.description }}</textarea>
				</label>
			</div>
		</div>
		<div class='row'>
			<div class='large-12 columns'>
				<label for="active_switch">Active
					<div class="switch">
						<input id="active_switch" name='is_active' type="checkbox" v-on:change='form_changed = true' v-bind:checked='setChecked()' v-model='application.is_active'>
						<label for='active_switch'></label>
					</div>
				</label>
			</div>
		</div>
		<div class='row' v-show='form_changed'>
			<div class='large-12 columns'>
				<a v-on:click='saveApplication()' class='button small'>Save</a>
				<a v-on:click='resetApplication()' class='button small secondary right'>Cancel</a>
			</div>
		</div>
	</form>

	<hr/>

	<div class='row-fluid'>
		<div class='small-12 medium-6 columns'>
			<h3>Members <button class='button tiny right' v-on:click='editApplicationMember()'>Add member</button></h3>
			<table style='width: 100%;' v-show='!loading_members'>
				<thead><tr><th>User</th><th>Role</th><th>Actions</th></tr></thead>
				<tbody>
					<tr v-if='application_members.length' v-for='member in application_members'>
						<td>{{ member.name }}</td>
						<td>{{ member.role }}</td>
						<td>
							<a class='button tiny secondary' v-on:click='editApplicationMember(member)'>Edit</a>
							<a class='button tiny danger' v-on:click='deleteApplicationMember(member)'>Delete</a>
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
							<a class='button tiny secondary' v-on:click='editApplicationForm(form)'>Edit</a>
							<a class='button tiny danger' v-on:click='deleteApplicationForm(form)'>Delete</a>
						</td>
					</tr>
					<tr v-if='!application_forms.length'><td colspan="3">No forms found</td></tr>
				</tbody>
			</table>
		</div>
	</div>
	<!-- Member modal -->
	<div id="form_application_member_modal" class="reveal-modal" data-reveal aria-labelledby="member_modalTitle" aria-hidden="true" role="dialog">
		<h2 id="member_modalTitle">{{ edit_modal_message }}</h2>
		<form v-on:submit='saveMember()'>
			<div class="row">
				<div class="large-12 columns">
					<autocomplete :list="user_list" v-on:autocomplete-select="setSelectedValue" property="name" :required="true" :threshold="1"></autocomplete>
				</div>
			</div>
		</form>
		<a class="close-reveal-modal" aria-label="Close">&#215;</a>
	</div>
</div>

<script src='/system/modules/form/assets/js/autocomplete.vue2.js'></script>
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
			edit_modal_message: '',
			active_member: {
				member_user_id: null,
				application_id: '<?php echo $application->id; ?>'
			},
			active_form: {},
			user_list: <?php echo json_encode(array_map(function($user) {return ['id' => $user->id, 'name' => $user->getFullName()];}, array_filter($w->Auth->getUsers(), function($user) {return !empty($user->id) && $user->is_active == 1 && $user->is_deleted == 0;})), true); ?>
		},
		methods: {
			setSelectedValue: function(selectedValue) {
				console.log("YOU SELECTED A VALUE", selectedValue);
			},
			setChecked: function() {
				if (this.application.is_active !== undefined && this.application.is_active == 1) {
					return true;
				}
				return false;
			},
			saveApplication: function() {

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
			deleteApplicationMember: function() {

			}
		},
		created: function() {
			this.getApplicationMembers();
			this.getApplicationForms();
		}
	});

</script>