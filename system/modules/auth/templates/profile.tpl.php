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
                    <label>Redirect URL
                        <?php
                        echo (new \Html\Form\InputField([
                            "id|name" => "redirect_url",
                        ]))->setAttribute("v-model", "user.account.redirect_url");
                        ?>
                    </label>
                </div>
                <div class="small-12 medium-6 large-4 columns">
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
                <div class="small-12 medium-6 large-4 columns">
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
        </html-tab>
        <html-tab title="Security">
            <profile-security :user-id="user.id.toString()" :is-mfa-enabled="Boolean(user.security.is_mfa_enabled)"></profile-security>
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
            }
        }
    })
</script>