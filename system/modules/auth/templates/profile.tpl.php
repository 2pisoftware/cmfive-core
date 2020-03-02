<?php
$w->setTemplatePath(SYSTEM_PATH . "/templates");
$w->setLayout("layout")
?>
<div id="app">
    <div class="tabs">
        <div class="tab-head">
            <a class="active" href="#account">Account</a>
            <a href="#security">Security</a>
        </div>
        <div class="tab-body">
            <div id="account">
                <div class="row-fluid body panel clearfix">
                    <div class="small-12 medium-6 large-4 columns">
                        <h3>General</h3>
                        <label for="redirect_url">Redirect URL</label>
                        <input id="redirect_url" type="text" v-model="user.account.redirect_url">
                    </div>
                    <div class="small-12 medium-6 large-4 columns">
                        <h3>Personal</h3>
                        <label for="title">Title</label>
                        <input id="title" type="text" v-model="user.account.title">
                        <label for="firstname">First Name</label>
                        <input id="firstname" type="text" v-model="user.account.firstname">
                        <label for="lastname">Last Name</label>
                        <input id="lastname" type="text" v-model="user.account.lastname">
                        <label for="othername">Other Name</label>
                        <input id="othername" type="text" v-model="user.account.othername">
                        <label for="language">Language</label>
                        <select id="language">
                            <option value="">-- Select --</option>
                        </select>
                    </div>
                    <div class="small-12 medium-6 large-4 columns">
                        <h3>Contact</h3>
                        <label for="homephone">Home Phone</label>
                        <input id="homephone" type="tel" v-model="user.account.homephone">
                        <label for="workphone">Work Phone</label>
                        <input id="workphone" type="tel" v-model="user.account.workphone">
                        <label for="mobile">Mobile</label>
                        <input id="mobile" type="tel" v-model="user.account.mobile">
                        <label for="priv_mobile">Private Mobile</label>
                        <input id="priv_mobile" type="tel" v-model="user.account.priv_mobile">
                        <label for="fax">Fax</label>
                        <input id="fax" type="text" v-model="user.account.fax">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" v-model="user.account.email">
                    </div>
                    <div class="small-12 columns">
                        <br>
                        <button class="tiny" @click="updateAccountDetails" :disabled="is_loading">Update</button>
                    </div>
                </div>
            </div>
            <div id="security">
                <div class="row-fluid body panel clearfix">
                    <div class="small-12 medium-6 columns">
                        <h3>Update Password</h3>
                        <form>
                            <label for="password">New Password</label>
                            <input id="password" type="password" v-model="user.security.new_password" required>
                            <label for="repeatpassword">Repeat New Password</label>
                            <input id="repeatpassword" type="password" v-model="user.security.repeat_new_password" required>
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
                                <div class="columns small-12">
                                    <img :src="mfa_qr_code_url" width="250" height="250">
                                </div>
                                <div class="columns small-12">
                                    <form>
                                        <label for="code">Code</label>
                                        <input id="code" type="text" v-model="mfa_code" required>
                                        <br>
                                        <button class="tiny success" @click="confirmMfaCode" :disabled="is_loading">Confirm Code</button>
                                        <button class="tiny info" @click="mfa_qr_code_url = null">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var app = new Vue({
        el: "#app",
        data: function() {
            return {
                user: <?php echo json_encode($user); ?>,
                mfa_code: "",
                mfa_qr_code_url: null,
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
            }
        }
    })
</script>