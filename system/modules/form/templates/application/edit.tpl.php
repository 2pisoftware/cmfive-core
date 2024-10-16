<style>
    #form-application-<?php echo $application->id; ?>__vue-instance tr > td > a {
        margin-bottom: 0px;
    }
</style>
<div id='form-application-<?php echo $application->id; ?>__vue-instance' style='background-color: #efefef; padding-top: 20px;'>
    <div class='row'>
        <div class='small-12 columns'>
            <h3>Editing Application: {{ application.title }} <a class='button tiny secondary right' style='margin-bottom: 0px;' href='/form-application/show/<?php echo $application->id; ?>'>View Application</a><a class='button tiny secondary right' style='margin-bottom: 0px;' href='/form-application/export/<?php echo $application->id; ?>'>Export Application</a></h3>
        </div>
    </div>
    <form style='margin-top: 0px;' v-on:submit='saveApplication()'>
        <form-row label="Title">
            <input type="text" v-model='application.title' v-on:keyup.prevent='form_changed = true' />
        </form-row>
        <form-row label="Description">
            <textarea v-model='application.description' v-on:keyup.prevent='form_changed = true'>{{ application.description }}</textarea>
        </form-row>
        <form-row label="Active" label-for="active_switch">
            <div class="switch">
                <input id="active_switch" name='is_active' type="checkbox" v-on:change='form_changed = true' :checked='setChecked()' v-model='application.is_active' :true-value='1' :false-value='0'>
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
                    <tr v-if='application_members.length' v-for='(member, index) in application_members'>
                        <td>{{ member.name }}</td>
                        <td>{{ member.role }}</td>
                        <td>
                            <a class='button tiny' v-on:click='editApplicationMember(index)'>Edit</a>
                            <a class='button tiny warning' v-on:click='deleteApplicationMember(member)'>Delete</a>
                        </td>
                    </tr>
                    <tr v-if='!application_members.length'><td colspan="3">No members found</td></tr>
                </tbody>
            </table>
        </div>
        <div class='small-12 medium-6 columns'>
            <h3>Attached Forms <button class='button tiny right' v-on:click='editApplicationForm()'>Attach form</button></h3>
            <loading-indicator :show="loading_forms"></loading-indicator>
            <table style='width: 100%;' v-show='!loading_forms'>
                <thead><tr><th>Form</th><th># saved rows</th><th>Actions</th></tr></thead>
                <tbody>
                    <tr v-if='application_forms.length' v-for='form in application_forms'>
                        <td>{{ form.title }}</td>
                        <td><span v-if="form.no_instances">{{ form.no_instances }}</span><span v-else>0</span></td>
                        <td>
                            <!-- <a class='button tiny' v-on:click='editApplicationForm(form)'>Edit</a> -->
                            <a class='button tiny warning' v-on:click='deleteApplicationForm(form)'>Delete</a>
                        </td>
                    </tr>
                    <tr v-if='!application_forms.length'><td colspan="3">No forms found</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form modal -->
    <modal id="form_application_form_modal" modal-title="Attach Form">
        <form v-on:submit='saveForm()'>
            <form-row label="Form">
                <select v-model='active_form.id'>
                    <option v-for="form in available_forms" :value="form.id">{{ form.title }}</option>
                </select>
            </form-row>
            <br/>
            <form-row>
                <button v-on:click.prevent='saveApplicationForm()' class='button tiny'>Save</button>
                <button v-on:click.prevent='resetActiveForm()' class='button tiny secondary right'>Cancel</button>
            </form-row>
        </form>
    </modal>

    <!-- Member modal -->
    <modal id="form_application_member_modal" :modal-title="getMemberModalTitle">
        <form v-on:submit='saveMember()'>
            <form-row label="User">
                <autocomplete :list="user_list" v-on:autocomplete-select="setSelectedValue" property="name" :required="true" :threshold="1" :value="active_member.name"></autocomplete>
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
    </modal>
</div>

<script src='/system/templates/vue-components/modal.vue.js'></script>

<link rel='stylesheet' href='/system/templates/vue-components/form/elements/autocomplete.vue.css' />
<script src='/system/templates/vue-components/form/elements/autocomplete.vue.js'></script>

<link rel='stylesheet' href='/system/templates/vue-components/loading-indicator.vue.css' />
<script src='/system/templates/vue-components/loading-indicator.vue.js'></script>

<script src='/system/templates/vue-components/form/form-row.vue.js'></script>
<script>
<?php if ($w->_layout === "layout-bootstrap-5") : ?>
    const {
        createApp
    } = Vue;
    createApp({
<?php else : ?>
    const form-application-<?php echo $application->id; ?>__vue-instance = new Vue({
        el: "#form-application-<?php echo $application->id; ?>__vue-instance",
<?php endif; ?>
        data: {
            shadow_application: <?php echo json_encode($application->toArray(), JSON_FORCE_OBJECT); ?>,
            application: <?php echo json_encode($application->toArray(), JSON_FORCE_OBJECT); ?>,
            form_changed: false,
            application_members: {},
            application_forms: {},
            loading_application: false,
            loading_members: true,
            loading_forms: true,

            available_forms: <?php echo json_encode(array_map(fn ($available_form) => ['id' => $available_form->id, 'title' => $available_form->title], $available_forms ? : [])); ?>,
            member_role_options: <?php echo json_encode(FormApplicationMember::$_roles, true); ?>,
            active_member: {id: '', member_user_id: '', name: '', role: '', application_id: '<?php echo $application->id; ?>'},
            active_form: {id: '', title: '', application_id: '<?php echo $application->id; ?>'},
            user_list: <?php echo json_encode(array_map(fn ($user) => ['id' => $user->id, 'name' => $user->getFullName()], array_filter(AuthService::getInstance($w)->getUsers(), fn ($user) => !empty($user->id) && $user->is_active == 1 && $user->is_deleted == 0)), true); ?>
        },
        methods: {
            setSelectedValue: function(selectedValue) {
                this.active_member.member_user_id = selectedValue;
            },
            setChecked = () => this.application.is_active !== undefined && this.application.is_active == 1),
            saveApplication: async function() {
                // var _this = this;
                const response = await fetch('/form-vue/save_application/<?php echo $application->id; ?>', {
                    method: "POST",
                    body: JSON.stringify(this.application),
                });
                const data = await response.json();
                if (data.success === true) {
                    this.shadow_application = Vue.util.extend({}, this.application);
                }
                this.form_changed = false;
            },
            resetApplication: function() {
                this.application = Vue.util.extend({}, this.shadow_application);
                this.form_changed = false;
            },
            getApplicationForms: async function() {
                this.loading_forms = true;
                const response = await fetch('/form-vue/get_forms/<?php echo $application->id; ?>')
                const data = await response.json();
                if (data.success) {
                    this.application_forms = data.data;
                } else {
                    alert(data.error);
                }

                this.loading_forms = false;
            },
            getApplicationMembers: function() {
                this.loading_members = true;
                const response = await fetch('/form-vue/get_members/<?php echo $application->id; ?>');
                const data = await response.json();
                if (data.success) {
                    this.application_members = data.data;
                } else {
                    alert(data.error);
                }

                this.loading_members = false;
            },
            editApplicationForm: function(form_index) {
                if (form_index !== undefined && ((this.application_forms.length - 1) >= form_index)) {
                    // this.active_form = Vue.util.extend({}, this.application_forms[form_index]);
                    this.active_form.id = this.application_forms[form_index].id;
                    this.active_form.title = this.application_forms[form_index].title;
                }
                $('#form_application_form_modal').foundation('reveal', 'open');
            },
            deleteApplicationForm: async function(form) {
                if (form.id !== undefined && confirm("Are you sure you want to detach this form? (You can reattach it later if needed)")) {
                    await fetch('/form-vue/delete_form/<?php echo $application->id; ?>/' + form.id);
                    this.getApplicationForms();
                }
            },
            editApplicationMember: function(member_index) {
                if (member_index !== undefined && ((this.application_members.length - 1) >= member_index)) {
                      this.active_member = Vue.util.extend({}, this.application_members[member_index]);
                } else {
                    this.active_member = {id: '', member_user_id: '', name: '', role: '', application_id: '<?php echo $application->id; ?>'};
                }
                $('#form_application_member_modal').foundation('reveal', 'open');
            },
            saveApplicationMember: function() {
                if (this.active_member.id != undefined) {
                    await fetch('/form-vue/save_member', {
                        method: 'POST',
                        body: JSON.stringify(this.active_member),
                    });
                    this.getApplicationMembers();
                    this.resetActiveMember();
                }
            },
            saveApplicationForm: async function() {
                if (this.active_form.id != undefined) {
                    await fetch('/form-vue/save_form', {
                        method: 'POST',
                        body: JSON.stringify(this.active_form),
                    });
                    this.getApplicationForms();
                    this.resetActiveForm();
                }
            },
            resetActiveMember: function() {
                this.active_member = {id: '', member_user_id: '', name: '', role: '', application_id: '<?php echo $application->id; ?>'};

                $('#form_application_member_modal').foundation('reveal', 'close');
            },
            resetActiveForm: function() {
                this.active_form = {id: '', title: '', application_id: '<?php echo $application->id; ?>'};

                $('#form_application_form_modal').foundation('reveal', 'close');
            },
            deleteApplicationMember: function(member) {
                if (member.id !== undefined && confirm("Are you sure you want to remove this member? (You can re-add them later if needed)")) {
                    await fetch('/form-vue/delete_member/<?php echo $application->id; ?>/' + member.id);
                    this.getApplicationMembers();
                }
            }
        },
        computed: {
            getMemberModalTitle: function() {
                return this.active_member.id != undefined && this.active_member.id != null && this.active_member != '' ? 'Edit member' : 'Create member';
            }
        },
        created: function() {
            this.getApplicationMembers();
            this.getApplicationForms();
        }
    }).mount("#form-application-<?php echo $application->id; ?>__vue-instance");

</script>