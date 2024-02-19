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
            <div class='row'>
                <label for='mfa_code' class='col-form-label'>MFA Code
                    <?php
                    echo (new \Html\Form\InputField([
                        "id|name" => "mfa_code",
                        "placeholder" => "Your code",
                        "required" => true,
                        "class" => "form-control-lg",
                    ]))->setAttribute("v-model", "mfa_code");
                    ?>
                </label>
            </div>
            <div class='row d-flex justify-content-between mt-3'>
                <div class='col'>
                    <button type="submit" class="btn btn-primary w-100">Confirm</button>
                </div>
                <div class='col'>
                    <button class="btn btn-secondary w-100" @click.prevent="back">Back</button>
                </div>
            </div>
        </form>
    </div>
    <div v-else>
        <form @submit.prevent="executeLogin">
            <div data-alert class="alert alert-warning fade show row d-flex justify-content-between" v-if="error_message != null" role='alert'>

                {{ error_message }}
                <button type='button' class="btn-close" @click="error_message = null" aria-label='Close'></button>
            </div>
            <?php
            echo (new \Html\Form\InputField\Hidden([
                "name" => CSRF::getTokenID(),
                "value" => CSRF::getTokenValue(),
            ]));
            ?>
            <div class='row'>
                <label for='login' class='col-form-label'><?php echo Config::get('auth.login_label', 'Login'); ?>
                    <?php
                    echo (new \Html\Form\InputField([
                        "id|name" => "login",
                        "placeholder" => Config::get('auth.login_label', 'Login'),
                        "required" => true,
                        "class" => "form-control-lg",
                    ]))->setAttribute("v-model", "login");
                    ?>
                </label>
                <label for='password' class='col-form-label'>Password
                    <?php
                    echo (new \Html\Form\InputField\Password([
                        "id|name" => "password",
                        "placeholder" => "Your password",
                        "required" => true,
                        "class" => "form-control-lg",
                    ]))->setAttribute("v-model", "password");
                    ?>
                </label>
            </div>
            <div class='row d-flex justify-content-between mt-3'>
                <div class='col'>
                    <button type="submit" class="btn btn-primary w-100 h-auto">Login</button>
                </div>
                <div class='col text-end'>
                    <a onclick="window.location.href='/auth/forgotpassword';" class="btn w-auto text-end"><?php echo $passwordHelp; ?></a>
                </div>
            </div>
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

                let formData = {
                    "<?php echo CSRF::getTokenID(); ?>": "<?php echo CSRF::getTokenValue(); ?>",
                    "login": _this.login,
                    "password": _this.password,
                    "mfa_code": _this.mfa_code,
                }

                fetch('/auth/login', {
                        method: 'POST',
                        headers: new Headers({
                            'Content-Type': 'application/json'
                        }),
                        body: JSON.stringify(formData)
                    }).then(response => {
                        return response.json()
                    }).then(response => {
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

                        if (response.status == 500) {
                            _this.login = null,
                                _this.password = null,
                                _this.mfa_code = null,
                                _this.is_mfa_enabled = false;
                            _this.error_message = response.message;
                        }
                    })
                    .catch(error => {
                        _this.login = null,
                            _this.password = null,
                            _this.mfa_code = null,
                            _this.is_mfa_enabled = false;
                        _this.error_message = error.message;
                    }).finally(() => {
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