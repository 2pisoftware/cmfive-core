<div id="user_edit_app">
    <h3>Edit - {{ user.account.firstname + ' ' + user.account.lastname }}</h3>
    <div class='tabs'>
        <div class='tab-head'>
            <a class='active' href="#tab-1">User Details</a>
            <a href="#tab-2">Security</a>
            <a href="#tab-3">Groups</a>
        </div>
        <div class='tab-body'>
            <div id='tab-1'><?php echo $userDetails; ?></div>
            <div id='tab-2'>
                <!-- <div class="container-fluid"> -->
                <div class="row">
                    <div class="col-6">
                        <form>
                            <div data-alert class="row mb-4 alert-box warning" v-if="user.security.is_locked">
                                This account is locked <input class="btn btn-danger" value="Unlock" style="font-size: 0.8rem; display: inline; float: middle; width: 100px; margin-top: -8px; margin-left: 15px;" @click.prevent="unlockAccount" :disabled="is_loading">
                            </div>
                        </form>
                        <div class='panel mt-0 clearfix'>
                            <div class='row g-0 clearfix section-header'>
                                <h4 class='col'>Update Password</h4>
                            </div>
                            <div class='row'>
                                <form class='col'>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input id="password" name="password" type="password" required="required" v-model="user.security.new_password" :minlength="enforcePasswordLength ? passwordMinLength : null" class="form-control" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="repeatpassword" class="form-label">Repeat New Password</label>
                                        <input id="repeatpassword" name="repeatpassword" type="password" required="required" v-model="user.security.repeat_new_password" :minlength="enforcePasswordLength ? passwordMinLength : null" class="form-control" />
                                    </div>
                                    <button class="btn btn-primary btn-sm m-0" type="submit" @click.prevent="updatePassword" :disabled="is_loading">Update Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class='panel clearfix'>
                            <div class='row g-0 clearfix section-header'>
                                <h4 class='col'>Google Authenticator</h4>
                            </div>
                            <button v-if="!user.security.is_mfa_enabled && mfa_qr_code_url === null" class="btn btn-success" @click="getMfaQrCode" :disabled="is_loading">Enable</button>
                            <button v-if="user.security.is_mfa_enabled" class="btn btn-warning" @click="disableMfa" :disabled="is_loading">Disable</button>
                            <div v-if="mfa_qr_code_url !== null">
                                <div v-if="show_qr_code">
                                    <img :src="mfa_qr_code_url" width="250" height="250">
                                    <label style="display: block; margin-top: 4px;">Can't scan the code? Add it manually.</label>
                                    <label>{{ mfa_secret }}</label>
                                </div>
                                <button v-else class="btn btn-primary" @click="show_qr_code = true">Show QR Code</button>
                                <form>
                                    <div class="mb-3">
                                        <label>Code</label>
                                        <input id="code" name="code" type="text" required="required" v-model="mfa_code" class="form-control" />
                                    </div>
                                    <button class="btn btn-primary" @click.prevent="confirmMfaCode" :disabled="is_loading">Confirm Code</button>
                                    <button class="btn btn-secondary" @click.prevent="cancel">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- </div> -->
            </div>
            <div id='tab-3'>
                <div class="row-fluid body panel clearfix">
                    <h3>Groups</h3>
                    <ul>
                        <li v-for="group in user.groups" :key="group.title">
                            <a :href="group.url" target="_blank">{{ group.title }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const {
        createApp
    } = Vue

    createApp({
        data: function() {
            return {
                user: <?php echo json_encode($user); ?>,
                mfa_code: "",
                mfa_secret: "",
                mfa_qr_code_url: null,
                show_qr_code: false,
                is_confirming_code: false,
                is_loading: false,
                enforcePasswordLength: <?php echo Config::get('auth.login.password.enforce_length') ? 'true' : 'false'; ?>,
                passwordMinLength: "<?php echo Config::get('auth.login.password.min_length', 8); ?>",
            }
        },
        methods: {
            async unlockAccount() {
                this.is_loading = true;

                const response = await fetch("/admin-user/ajax_unlock_account", {
                    method: "POST",
                    body: JSON.stringify({
                        id: this.user.id
                    })
                }).catch((error) => {
                    (new window.cmfive.toast("Failed to update")).show();
                    console.log(error);
                    this.is_loading = false;
                });

                const json_response = await response.json();

                if (response.status !== 200) {
                    (new window.cmfive.toast("Failed to unlock")).show();
                    return;
                }

                (new window.cmfive.toast("Account unlocked")).show();
                this.user.security.is_locked = false;

                this.is_loading = false;
            },
            async updatePassword() {
                console.log(this.user.security)
                this.user.security.new_password = this.user.security.new_password.trim();
                this.user.security.repeat_new_password = this.user.security.repeat_new_password.trim();

                if (this.is_loading === true || this.user.security.new_password === "" || this.user.security.repeat_new_password === "") {
                    return;
                }

                if (this.user.security.new_password !== this.user.security.repeat_new_password) {
                    (new window.cmfive.toast("Passwords don't match")).show()
                    return;
                }

                if (this.enforcePasswordLength === true) {
                    if (this.user.security.new_password.length < this.passwordMinLength) {
                        (new window.cmfive.toast(`Passwords must be at least ${this.passwordMinLength} characters long`)).show()
                        return;
                    }
                }

                this.is_loading = true;

                const response = await fetch("/auth/ajax_update_password", {
                    method: "POST",
                    body: JSON.stringify({
                        id: this.user.id,
                        new_password: this.user.security.new_password,
                        repeat_new_password: this.user.security.repeat_new_password
                    })
                }).catch(function(error) {
                    (new window.cmfive.toast("Failed to update")).show();
                    console.log(error);
                    this.is_loading = false;
                })

                const json_response = await response.json();

                if (response.status !== 200) {
                    (new window.cmfive.toast("Failed to update")).show();
                    return;
                }

                this.user.security.new_password = "";
                this.user.security.repeat_new_password = "";
                (new window.cmfive.toast(json_response.message)).show();

                this.is_loading = false;
            },
            async getMfaQrCode() {
                if (this.is_loading === true) {
                    return;
                }

                this.is_loading = true;

                const response = await fetch(`/auth/ajax_get_mfa_qr_code?id=${this.user.id}`)
                    .catch(function(error) {
                        (new window.cmfive.toast("Failed to fetch QR Code")).show();
                        this.is_loading = false;
                        console.log(error);
                    })
                const json_response = await response.json();

                if (response.status !== 200) {
                    (new window.cmfive.toast("Failed to fetch QR Code")).show();
                    this.is_loading = false;
                    return;
                }

                this.mfa_qr_code_url = json_response.data.qr_code;
                this.mfa_secret = json_response.data.mfa_secret;

                this.is_loading = false;
            },
            async confirmMfaCode() {
                this.mfa_code = this.mfa_code.trim();

                if (this.is_loading === true || this.mfa_code === "") {
                    return;
                }

                this.is_loading = true;

                const response = await fetch("/auth/ajax_confirm_mfa_code", {
                    method: "POST",
                    body: JSON.stringify({
                        id: this.user.id,
                        mfa_code: this.mfa_code
                    })
                }).catch(function(error) {
                    (new window.cmfive.toast("Failed to confirm 2FA Code")).show();
                    this.is_loading = false;
                    console.log(error);
                })

                const json_response = await response.json();
                if (response.status !== 200) {
                    (new window.cmfive.toast("Failed to confirm 2FA Code")).show();
                    this.is_loading = false;
                    return;
                }

                this.mfa_qr_code_url = null;
                this.mfa_secret = null;
                this.user.security.is_mfa_enabled = true;
                (new window.cmfive.toast("2FA enabled")).show();

                this.is_loading = false;
            },
            async disableMfa() {
                if (!confirm("Are you sure you want to disable 2FA? This will greatly decrease the security of your account.")) {
                    return;
                }

                if (this.is_loading === true) {
                    return;
                }

                this.is_loading = true;

                const response = await fetch("/auth/ajax_disable_mfa", {
                    method: "POST",
                    body: JSON.stringify({
                        id: this.user.id
                    })
                }).catch(function(error) {
                    (new window.cmfive.toast("Failed to disable 2FA")).show();
                    this.is_loading = false;
                })
                const json_response = response.json();
                if (response.status !== 200) {
                    (new window.cmfive.toast("Failed to disable 2FA")).show();
                    this.is_loading = false;
                    return;
                }

                this.user.security.is_mfa_enabled = false;
                (new window.cmfive.toast("2FA disabled")).show();

                this.is_loading = false;
            },
            cancel: function() {
                this.mfa_qr_code_url = null;
                this.show_qr_code = false;
            }
        }
    }).mount("#user_edit_app")
</script>