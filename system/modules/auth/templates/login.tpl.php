<div id="app">
    <div v-if="is_mfa_enabled">
        <form @submit="executeLogin">
            <?php
            echo (new \Html\Form\InputField\Hidden([
                "name" => CSRF::getTokenID(),
                "value" => CSRF::getTokenValue(),
            ]))->__toString() .
            (new \Html\Form\InputField\Hidden([
                "name" => "login",
            ]))->setAttribute("v-model", "login")->__toString() .
            (new \Html\Form\InputField\Hidden([
                "name" => "password",
            ]))->setAttribute("v-model", "password")->__toString();
            ?>
            <label for="mfa_code">MFA Code</label>
            <?php
            echo (new \Html\Form\InputField([
                "id" => "mfa_code",
                "placeholder" => "Your code",
                "required" => true,
            ]))->setAttribute("v-model", "mfa_code")->__toString();
            ?>
            <button type="submit" class="button medium-5 small-12">Confirm</button>
            <button class="button info medium-5 small-12 right" @click="back">Back</button>
        </form>
    </div>
    <div v-else>
        <form @submit="executeLogin">
            <div data-alert class="alert-box alert" v-if="error_message != null">
                {{ error_message }}
                <a href="#" class="close" @click="error_message = null">&times;</a>
            </div>
            <?php
            echo (new \Html\Form\InputField\Hidden([
                "name" => CSRF::getTokenID(),
                "value" => CSRF::getTokenValue(),
            ]))->__toString();
            ?>
            <label for="login">Login</label>
            <?php
            echo (new \Html\Form\InputField([
                "id" => "login",
                "placeholder" => "Your login",
                "required" => true,
            ]))->setAttribute("v-model", "login")->__toString();
            ?>
            <label for="password">Password</label>
            <?php
            echo (new \Html\Form\InputField\Password([
                "id" => "password",
                "placeholder" => "Your password",
                "required" => true,
            ]))->setAttribute("v-model", "password")->__toString();
            ?>
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
            executeLogin(e) {
                e.preventDefault();
                var _this = this;

                if (_this.is_loading) {
                    return;
                }

                _this.is_loading = true;

                axios.post("/auth/login", {
                    <?php echo '"' . CSRF::getTokenID() . '"'; ?>: <?php echo '"' . CSRF::getTokenValue() . '"'; ?>,
                    login: _this.login,
                    password: _this.password,
                    mfa_code: _this.mfa_code,
                }).then(function(response) {
                    if (response.data.redirect_url != null) {
                        window.location.href = response.data.redirect_url;
                        return;
                    }

                    _this.is_mfa_enabled = response.data.is_mfa_enabled;
                }).catch(function(error) {
                    _this.login = null,
                    _this.password = null,
                    _this.mfa_code = null,
                    _this.is_mfa_enabled = false;
                    _this.error_message = "Incorrect login details";
                }).finally(function() {
                    _this.is_loading = false;
                });
            },
            back(e) {
                e.preventDefault();
                this.is_mfa_enabled = false;
                this.mfa_code = null;
            }
        }
    })
</script>