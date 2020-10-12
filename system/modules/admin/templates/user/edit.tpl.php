<div id="app">
    <h3>Edit - {{ user.account.firstname + ' ' + user.account.lastname }}</h3>
    <html-tabs>
        <html-tab title="Account" selected>
            <div id="account">
                <div class="row-fluid body panel clearfix">
                    <div class="small-12 medium-6 large-6 columns">
                        <h3>Personal</h3>
                        <label>Title
                            <autocomplete :list="user.account.titles" v-model="user.account.title"></autocomplete>
                        </label>
                        <label>First Name
                            <?php
                            echo (new \Html\Form\InputField([
                                "id|name" => "firstname",
                            ]))->setAttribute("v-model", "user.account.firstname");
                            ?>
                        </label>
                        <label>Last Name
                            <?php
                            echo (new \Html\Form\InputField([
                                "id|name" => "lastname",
                            ]))->setAttribute("v-model", "user.account.lastname");
                            ?>
                        </label>
                        <label>Other Name
                            <?php
                            echo (new \Html\Form\InputField([
                                "id|name" => "othername",
                            ]))->setAttribute("v-model", "user.account.othername");
                            ?>
                        </label>
                        <label>Language
                            <?php
                            echo (new \Html\Form\Select([
                                "id|name" => "language",
                            ]))->setOptions([]);
                            ?>
                        </label>
                    </div>
                    <div class="small-12 medium-6 large-6 columns">
                        <h3>Contact</h3>
                        <label>Home Phone
                            <?php
                            echo (new \Html\Form\InputField\Tel([
                                "id|name" => "homephone",
                            ]))->setAttribute("v-model", "user.account.homephone");
                            ?>
                        </label>
                        <label>Work Phone
                            <?php
                            echo (new \Html\Form\InputField\Tel([
                                "id|name" => "workphone",
                            ]))->setAttribute("v-model", "user.account.workphone");
                            ?>
                        </label>
                        <label>Mobile
                            <?php
                            echo (new \Html\Form\InputField\Tel([
                                "id|name" => "mobile",
                            ]))->setAttribute("v-model", "user.account.mobile");
                            ?>
                        </label>
                        <label>Private Mobile
                            <?php
                            echo (new \Html\Form\InputField\Tel([
                                "id|name" => "priv_mobile",
                            ]))->setAttribute("v-model", "user.account.priv_mobile");
                            ?>
                        </label>
                        <label>Fax
                            <?php
                            echo (new \Html\Form\InputField([
                                "id|name" => "fax",
                            ]))->setAttribute("v-model", "user.account.fax");
                            ?>
                        </label>
                        <label>Email Address
                            <?php
                            echo (new \Html\Form\InputField\Email([
                                "id|name" => "email",
                            ]))->setAttribute("v-model", "user.account.email");
                            ?>
                        </label>
                    </div>
                    <div class="small-12 columns">
                        <br>
                        <button class="tiny" @click="updateAccountDetails" :disabled="is_loading">Update</button>
                    </div>
                </div>
            </div>
        </html-tab>
        <html-tab title="Security">
            <div class="row-fluid body panel clearfix">
                <div class="small-12 medium-6 large-4 columns">
                    <h3>General</h3>
                    <form>
                        <label>Login
                            <?php
                            echo (new \Html\Form\InputField([
                                "id|name" => "login",
                                "required" => true,
                            ]))->setAttribute("v-model", "user.security.login");
                            ?>
                        </label>
                        <label>Admin
                            <?php
                            echo (new \Html\Form\InputField\Checkbox([
                                "id|name" => "admin",
                                "class" => "",
                            ]))->setAttribute("v-model", "user.security.is_admin");
                            ?>
                        </label>
                        <label>Active
                            <?php
                            echo (new \Html\Form\InputField\Checkbox([
                                "id|name" => "active",
                                "class" => "",
                            ]))->setAttribute("v-model", "user.security.is_active");
                            ?>
                        </label>
                        <label>External
                            <?php
                            echo (new \Html\Form\InputField\Checkbox([
                                "id|name" => "external",
                                "class" => "",
                            ]))->setAttribute("v-model", "user.security.is_external");
                            ?>
                        </label>
                        <br>
                        <input class="button tiny" type="submit" value="Update" style="font-size: 0.8rem;" @click.prevent="updateSecurityDetails" :disabled="is_loading">
                    </form>
                </div>
                <div class="small-12 medium-6 large-4 columns">
                    <h3>Update Password</h3>
                    <form>
                        <label>New Password
                            <?php
                            echo (new \Html\Form\InputField\Password([
                                "id|name" => "password",
                                "required" => true,
                            ]))->setAttribute("v-model", "user.security.new_password");
                            ?>
                        </label>
                        <label>Repeat New Password
                            <?php
                            echo (new \Html\Form\InputField\Password([
                                "id|name" => "repeatpassword",
                                "required" => true,
                            ]))->setAttribute("v-model", "user.security.repeat_new_password");
                            ?>
                        </label>
                        <br>
                        <br>
                        <input class="button tiny" type="submit" value="Update Password" style="font-size: 0.8rem;" @click.prevent="updatePassword" :disabled="is_loading">
                    </form>
                </div>
                <div class="small-12 medium-6 large-4 columns end">
                    <div class="large-12">
                        <h3>Two Factor Authentication</h3>
                    </div>
                    <div class="large-12">
                        <label>Google Authenticator</label>
                        <button v-if="!user.security.is_mfa_enabled && mfa_qr_code_url === null" class="tiny success" @click="getMfaQrCode" :disabled="is_loading">Enable</button>
                        <button v-if="user.security.is_mfa_enabled" class="tiny alert" @click="disableMfa" :disabled="is_loading">Disable</button>
                        <div v-if="mfa_qr_code_url !== null">
                            <div v-if="show_qr_code">
                                <img :src="mfa_qr_code_url" width="250" height="250">
                                <label style="margin-top: 4px;">{{ mfa_secret }}</label>
                            </div>
                            <button v-else class="tiny" @click="show_qr_code = true">Show QR Code</button>
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
                                <button class="tiny success" @click.prevent="confirmMfaCode" :disabled="is_loading">Confirm Code</button>
                                <button class="tiny info" @click.prevent="cancel">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </html-tab>
        <html-tab title="Groups">
            <div class="row-fluid body panel clearfix">
                <h3>Groups</h3>
                <ul>
                    <li v-for="group in user.groups">
                        <a :href="group.url" target="_blank">{{ group.title }}</a>
                    </li>
                </ul>
            </div>
        </html-tab>
    </html-tabs>
</div>
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