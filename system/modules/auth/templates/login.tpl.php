<div v-cloak id="app">
    <div v-if="is_mfa_enabled">
        <form @submit.prevent="executeLogin">
            <?php
            echo (new \Html\Form\InputField\Hidden([
                "name" => CSRF::getTokenID(),
                "value" => CSRF::getTokenValue(),
            ])) .
            (new \Html\Form\InputField\Hidden([
                "name" => "login",
            ]))->setAttribute("v-model", "login") .
            (new \Html\Form\InputField\Hidden([
                "name" => "password",
            ]))->setAttribute("v-model", "password");
            ?>
            <label>MFA Code
                <?php
                echo (new \Html\Form\InputField([
                    "id|name" => "mfa_code",
                    "placeholder" => "Your code",
                    "required" => true,
                ]))->setAttribute("v-model", "mfa_code");
                ?>
            </label>
            <button type="submit" class="button medium-5 small-12">Confirm</button>
            <button class="button info medium-5 small-12 right" @click.prevent="back">Back</button>
        </form>
    </div>
    <div v-else>
        <form @submit.prevent="executeLogin">
            <div data-alert class="alert-box alert" v-if="error_message != null">
                {{ error_message }}
                <a href="#" class="close" @click="error_message = null">&times;</a>
            </div>
            <?php
            echo (new \Html\Form\InputField\Hidden([
                "name" => CSRF::getTokenID(),
                "value" => CSRF::getTokenValue(),
            ]));
            ?>
            <label>Login
                <?php
                echo (new \Html\Form\InputField([
                    "id|name" => "login",
                    "placeholder" => "Your login",
                    "required" => true,
                ]))->setAttribute("v-model", "login");
                ?>
            </label>
            <label>Password
                <?php
                echo (new \Html\Form\InputField\Password([
                    "id|name" => "password",
                    "placeholder" => "Your password",
                    "required" => true,
                ]))->setAttribute("v-model", "password");
                ?>
            </label>
            <button type="submit" class="button medium-5 small-12">Login</button>
            <a onclick="window.location.href='/auth/forgotpassword';" class="medium-5 small-12 right text-right"><?php echo $passwordHelp; ?></a>
        </form>
    </div>
</div>
<script>
    var app = new Vue({
        el: "#app",
        data: function() {
            return {
                login: null,
                password: null,
                mfa_code: null,
                error_message: null,
                is_mfa_enabled: false,
                is_loading: false,
            }
        },
        methods: {
            executeLogin: function() {
                var _this = this;

                if (_this.is_loading) {
                    return;
                }

                _this.is_loading = true;

                axios.post("/auth/login", {
                    "<?php echo CSRF::getTokenID(); ?>": "<?php echo CSRF::getTokenValue(); ?>",
                    login: _this.login,
                    password: _this.password,
                    mfa_code: _this.mfa_code,
                }).then(function(response) {
                    if (response.data.redirect_url != null) {
                        window.location.href = response.data.redirect_url;
                        return;
                    }

                    _this.is_mfa_enabled = response.data.is_mfa_enabled;
                    if (_this.is_mfa_enabled) {
                        _this.$nextTick(function() {
                            document.getElementById("mfa_code").focus();
                        });
                    }
                }).catch(function(error) {
                    _this.login = null,
                    _this.password = null,
                    _this.mfa_code = null,
                    _this.is_mfa_enabled = false;
                    _this.error_message = error.response.data;
                }).finally(function() {
                    _this.is_loading = false;
                });
            },
            back: function() {
                this.is_mfa_enabled = false;
                this.mfa_code = null;
            }
        }
    })
</script>