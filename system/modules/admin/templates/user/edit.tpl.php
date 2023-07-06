<div id="app">
    <h3>Edit - {{ user.account.firstname + ' ' + user.account.lastname }}</h3>
    <div class='tabs'>
        <div class='tab-head'>
            <a class='active' href="#tab-1">User Details</a>
            <a href="#tab-2">Security</a>
            <a href="#tab-3">Groups</a>
        </div>
        <div class='tab-body'>
            <div id='tab-1'>
                <?php echo $userDetails; ?>
            </div>
            <div id='tab-2'>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <form>
                                <div data-alert class="row mb-4 alert-box warning" v-if="user.security.is_locked">
                                    This account is locked <input class="btn btn-danger" value="Unlock" style="font-size: 0.8rem; display: inline; float: middle; width: 100px; margin-top: -8px; margin-left: 15px;" @click.prevent="unlockAccount" :disabled="is_loading">
                                </div>
                            </form>
                            <h3>Update Password</h3>
                            <form>
                                <label>New Password
                                    <?php
                                    $password_field = (new \Html\Form\InputField\Password([
                                        "id|name" => "password",
                                        "required" => true,
                                    ]))->setAttribute("v-model", "user.security.new_password");
                                    if (Config::get('auth.login.password.enforce_length') === true) {
                                        $password_field->setMinlength(Config::get('auth.login.password.min_length', 8));
                                    }
                                    echo $password_field;
                                    ?>
                                </label>
                                <label>Repeat New Password
                                    <?php
                                    $password_confirm = (new \Html\Form\InputField\Password([
                                        "id|name" => "repeatpassword",
                                        "required" => true,
                                    ]))->setAttribute("v-model", "user.security.repeat_new_password");
                                    if (Config::get('auth.login.password.enforce_length') === true) {
                                        $password_confirm->setMinlength(Config::get('auth.login.password.min_length', 8));
                                    }

                                    echo $password_confirm;
                                    ?>
                                </label>
                                <br>
                                <br>
                                <input class="button tiny" type="submit" value="Update Password" style="font-size: 0.8rem;" @click.prevent="updatePassword" :disabled="is_loading">
                            </form>
                        </div>
                        <div class="col">
                            <label>Google Authenticator</label>
                            <button v-if="!user.security.is_mfa_enabled && mfa_qr_code_url === null" class="btn btn-success" @click="getMfaQrCode" :disabled="is_loading">Enable</button>
                            <button v-if="user.security.is_mfa_enabled" class="btn btn-warning" @click="disableMfa" :disabled="is_loading">Disable</button>
                            <div v-if="mfa_qr_code_url !== null">
                                <div v-if="show_qr_code">
                                    <img :src="mfa_qr_code_url" width="250" height="250">
                                    <label style="margin-top: 4px;">Can't scan the code? Add it manually.</label>
                                    <label>{{ mfa_secret }}</label>
                                </div>
                                <button v-else class="btn btn-primary" @click="show_qr_code = true">Show QR Code</button>
                                <form>
                                    <label>Code
                                        <?php
                                        echo (new \Html\Form\InputField([
                                            "id|name" => "code",
                                            "required" => true,
                                        ]))->setAttribute("v-model", "mfa_code");
                                        ?>
                                    </label>
                                    <br>
                                    <br>
                                    <button class="btn btn-primary" @click.prevent="confirmMfaCode" :disabled="is_loading">Confirm Code</button>
                                    <button class="btn btn-secondary" @click.prevent="cancel">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id='tab-3'>
                <div class="row-fluid body panel clearfix">
                    <h3>Groups</h3>
                    <ul>
                        <li v-for="group in user.groups">
                            <a :href="group.url" target="_blank">{{ group.title }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/system/templates/js/axios.min.js"></script>

<script src="/system/templates/js/jquery-1.4.2.min.js"></script>
<!-- TODO: Needs toast SCSS too. -->
<script src="/system/templates/js/Toast.js"></script>
<script src="/system/templates/vue-components/profile-security.vue.js"></script>

<script>
    var app = new Vue({
        el: "#app",
        data: function() {
            return {
                user: <?php echo json_encode($user); ?>,
                mfa_code: "",
                mfa_secret: "",
                mfa_qr_code_url: null,
                show_qr_code: false,
                is_confirming_code: false,
                is_loading: false,
            }
        },
        methods: {
            /*
             * Now done in edit_POST function
             *
            updateAccountDetails: function() {
                var _this = this;

                if (_this.is_loading === true) {
                    return;
                }

                _this.is_loading = true;

                axios.post("/auth/ajax_update_account_details", {
                    id: _this.user.id,
                    account_details: _this.user.account
                }).then(function(response) {
                    if (response.status !== 200) {
                        new Toast("Failed to update").show();
                        return;
                    }

                    new Toast("Account details updated").show();
                }).catch(function(error) {
                    new Toast("Failed to update").show();
                    console.log(error);
                }).finally(function() {
                    _this.is_loading = false;
                });
            },
            updateSecurityDetails: function() {
                var _this = this;
                _this.user.security.login = _this.user.security.login.trim();

                if (_this.is_loading === true || _this.user.security.login === "") {
                    return;
                }

                _this.is_loading = true;

                axios.post("/admin-user/ajax_update_security_details", {
                    id: _this.user.id,
                    security_details: _this.user.security
                }).then(function(response) {
                    if (response.status !== 200) {
                        new Toast("Failed to update").show();
                        return;
                    }

                    new Toast("Security details updated").show();
                }).catch(function(error) {
                    new Toast("Failed to update").show();
                    console.log(error);
                }).finally(function() {
                    _this.is_loading = false;
                });
            },
            */
            unlockAccount: function() {
                this.is_loading = true;

                axios.post("/admin-user/ajax_unlock_account", {
                    id: this.user.id
                }).then((response) => {
                    if (response.status !== 200) {
                        new Toast("Failed to unlock").show();
                        return;
                    }

                    new Toast("Account unlocked").show();
                    this.user.security.is_locked = false;
                }).catch(function(error) {
                    new Toast("Failed to update").show();
                    console.log(error);
                }).finally(() => {
                    this.is_loading = false;
                });
            },
            updatePassword: function() {
                var _this = this;
                _this.user.security.new_password = _this.user.security.new_password.trim();
                _this.user.security.repeat_new_password = _this.user.security.repeat_new_password.trim();

                if (_this.is_loading === true || _this.user.security.new_password === "" || _this.user.security.repeat_new_password === "") {
                    return;
                }

                if (_this.user.security.new_password !== _this.user.security.repeat_new_password) {
                    new Toast("Passwords don't match").show();
                    return;
                }

                <?php if (Config::get('auth.login.password.enforce_length') === true) : ?>
                    if (_this.user.security.new_password.length < <?php echo Config::get('auth.login.password.min_length', 8); ?>) {
                        new Toast('Passwords must be at least <?php echo Config::get('auth.login.password.min_length', 8); ?> characters long').show()
                        return;
                    }
                <?php endif; ?>

                _this.is_loading = true;

                axios.post("/auth/ajax_update_password", {
                    id: _this.user.id,
                    new_password: _this.user.security.new_password,
                    repeat_new_password: _this.user.security.repeat_new_password
                }).then(function(response) {
                    if (response.status !== 200) {
                        new Toast("Failed to update").show();
                        return;
                    }

                    _this.user.security.new_password = "";
                    _this.user.security.repeat_new_password = "";
                    new Toast("Password updated").show();
                }).catch(function(error) {
                    new Toast("Failed to update").show();
                    console.log(error);
                }).finally(function() {
                    _this.is_loading = false;
                });
            },
            getMfaQrCode: function() {
                var _this = this;

                if (_this.is_loading === true) {
                    return;
                }

                _this.is_loading = true;

                axios.get("/auth/ajax_get_mfa_qr_code", {
                    params: {
                        id: _this.user.id
                    }
                }).then(function(response) {
                    if (response.status !== 200) {
                        new Toast("Failed to fetch QR Code").show();
                        return;
                    }

                    _this.mfa_qr_code_url = response.data.qr_code;
                    _this.mfa_secret = response.data.mfa_secret;
                }).catch(function(error) {
                    new Toast("Failed to fetch QR Code").show();
                    console.log(error);
                }).finally(function() {
                    _this.is_loading = false;
                });
            },
            confirmMfaCode: function() {
                var _this = this;
                _this.mfa_code = _this.mfa_code.trim();

                if (_this.is_loading === true || _this.mfa_code === "") {
                    return;
                }

                _this.is_loading = true;

                axios.post("/auth/ajax_confirm_mfa_code", {
                    id: _this.user.id,
                    mfa_code: _this.mfa_code
                }).then(function(response) {
                    if (response.status !== 200) {
                        new Toast("Failed to confirm 2FA Code").show();
                        return;
                    }

                    _this.mfa_qr_code_url = null;
                    _this.mfa_secret = null;
                    _this.user.security.is_mfa_enabled = true;
                    new Toast("2FA enabled").show();
                }).catch(function(error) {
                    new Toast("Failed to confirm 2FA Code").show();
                    console.log(error);
                }).finally(function() {
                    _this.is_loading = false;
                });
            },
            disableMfa: function() {
                if (!confirm("Are you sure you want to disable 2FA? This will greatly decrease the security of your account.")) {
                    return;
                }

                var _this = this;

                if (_this.is_loading === true) {
                    return;
                }

                _this.is_loading = true;

                axios.post("/auth/ajax_disable_mfa", {
                    id: _this.user.id
                }).then(function(response) {
                    if (response.status !== 200) {
                        new Toast("Failed to disable 2FA").show();
                        return;
                    }

                    _this.user.security.is_mfa_enabled = false;
                    new Toast("2FA disabled").show();
                }).catch(function(error) {
                    new Toast("Failed to disable 2FA").show();
                    console.log(error);
                }).finally(function() {
                    _this.is_loading = false;
                });
            },
            cancel: function() {
                this.mfa_qr_code_url = null;
                this.show_qr_code = false;
            }
        }
    })
</script>