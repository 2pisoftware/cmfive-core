<?php
$w->setTemplatePath(SYSTEM_PATH . "/templates");
$w->setLayout("layout")
?>
<div id="app">
    <h3>Edit Profile</h3>
    <html-tabs>
        <html-tab title="Account" selected>
            <div class="row-fluid body panel clearfix">
                <div class="small-12 medium-6 large-4 columns">
                    <h3>General</h3>
                    <label for="redirect_url">Redirect URL</label>
                    <?php
                    echo (new \Html\Form\InputField([
                        "id" => "redirect_url",
                    ]))->setAttribute("v-model", "user.account.redirect_url")->__toString();
                    ?>
                </div>
                <div class="small-12 medium-6 large-4 columns">
                    <h3>Personal</h3>
                    <label for="title">Title</label>
                    <autocomplete :list="user.account.titles" v-model="user.account.title"></autocomplete>
                    <label for="firstname">First Name</label>
                    <?php
                    echo (new \Html\Form\InputField([
                        "id" => "firstname",
                    ]))->setAttribute("v-model", "user.account.firstname")->__toString();
                    ?>
                    <label for="lastname">Last Name</label>
                    <?php
                    echo (new \Html\Form\InputField([
                        "id" => "lastname",
                    ]))->setAttribute("v-model", "user.account.lastname")->__toString();
                    ?>
                    <label for="othername">Other Name</label>
                    <?php
                    echo (new \Html\Form\InputField([
                        "id" => "othername",
                    ]))->setAttribute("v-model", "user.account.othername")->__toString();
                    ?>
                    <label for="language">Language</label>
                    <?php
                    echo (new \Html\Form\Select([
                        "id" => "language",
                    ]))->setOptions([])->__toString();
                    ?>
                </div>
                <div class="small-12 medium-6 large-4 columns">
                    <h3>Contact</h3>
                    <label for="homephone">Home Phone</label>
                    <?php
                    echo (new \Html\Form\InputField\Tel([
                        "id" => "homephone",
                    ]))->setAttribute("v-model", "user.account.homephone")->__toString();
                    ?>
                    <label for="workphone">Work Phone</label>
                    <?php
                    echo (new \Html\Form\InputField\Tel([
                        "id" => "workphone",
                    ]))->setAttribute("v-model", "user.account.workphone")->__toString();
                    ?>
                    <label for="mobile">Mobile</label>
                    <?php
                    echo (new \Html\Form\InputField\Tel([
                        "id" => "mobile",
                    ]))->setAttribute("v-model", "user.account.mobile")->__toString();
                    ?>
                    <label for="priv_mobile">Private Mobile</label>
                    <?php
                    echo (new \Html\Form\InputField\Tel([
                        "id" => "priv_mobile",
                    ]))->setAttribute("v-model", "user.account.priv_mobile")->__toString();
                    ?>
                    <label for="fax">Fax</label>
                    <?php
                    echo (new \Html\Form\InputField([
                        "id" => "fax",
                    ]))->setAttribute("v-model", "user.account.fax")->__toString();
                    ?>
                    <label for="email">Email Address</label>
                    <?php
                    echo (new \Html\Form\InputField\Email([
                        "id" => "email",
                    ]))->setAttribute("v-model", "user.account.email")->__toString();
                    ?>
                </div>
                <div class="small-12 columns">
                    <br>
                    <button class="tiny" @click="updateAccountDetails" :disabled="is_loading">Update</button>
                </div>
            </div>
        </html-tab>
        <html-tab title="Security">
            <div class="row-fluid body panel clearfix">
                <div class="small-12 medium-6 columns">
                    <h3>Update Password</h3>
                    <form>
                        <label for="password">New Password</label>
                        <?php
                        echo (new \Html\Form\InputField\Password([
                            "id" => "password",
                            "required" => true,
                        ]))->setAttribute("v-model", "user.security.new_password")->__toString();
                        ?>
                        <label for="repeatpassword">Repeat New Password</label>
                        <?php
                        echo (new \Html\Form\InputField\Password([
                            "id" => "repeatpassword",
                            "required" => true,
                        ]))->setAttribute("v-model", "user.security.repeat_new_password")->__toString();
                        ?>
                        <br>
                        <br>
                        <input class="button tiny" type="submit" value="Update Password" style="font-size: 0.8rem;" @click="updatePassword" :disabled="is_loading">
                    </form>
                </div>
                <div class="small-12 medium-6 columns end">
                    <div class="large-12">
                        <h3>Two Factor Authentication</h3>
                    </div>
                    <div class="large-12">
                        <label>Google Authenticator</label>
                        <button v-if="!user.security.is_mfa_enabled && mfa_qr_code_url === null" class="tiny success" @click="getMfaQrCode" :disabled="is_loading">Enable</button>
                        <button v-if="user.security.is_mfa_enabled" class="tiny alert" @click="disableMfa" :disabled="is_loading">Disable</button>
                        <div v-if="mfa_qr_code_url !== null">
                            <img v-if="show_qr_code" :src="mfa_qr_code_url" width="250" height="250">
                            <button v-else class="tiny" @click="show_qr_code = true">Show QR Code</button>
                            <form>
                                <label for="code">Code</label>
                                <?php
                                echo (new \Html\Form\InputField([
                                    "id" => "code",
                                    "required" => true,
                                ]))->setAttribute("v-model", "mfa_code")->__toString();
                                ?>
                                <br>
                                <br>
                                <button class="tiny success" @click="confirmMfaCode" :disabled="is_loading">Confirm Code</button>
                                <button class="tiny info" @click="cancel">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
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
            cancel: function(e) {
                e.preventDefault();
                this.mfa_qr_code_url = null;
                this.show_qr_code = false;
            }
        }
    })
</script>