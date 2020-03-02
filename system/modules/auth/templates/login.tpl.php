<div id="app">
    <div v-if="is_mfa_enabled">
        <form @submit="executeLogin">
            <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
            <input type="hidden" name="login" v-model="login" />
            <input type="hidden" name="password" v-model="password" />
            <label for="mfa_code">MFA Code</label>
            <input id="mfa_code" name="mfa_code" type="text" placeholder="Your code" v-model="mfa_code" required />
            <button type="submit" class="button medium-5 small-12">Confirm</button>
        </form>
    </div>
    <div v-else>
        <form @submit="executeLogin">
            <div data-alert class="alert-box alert" v-if="error_message != ''">
                {{ error_message }}
                <a href="#" class="close" @click="error_message = ''">&times;</a>
            </div>
            <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
            <label for="login">Login</label>
            <input id="login" name="login" type="text" placeholder="Your login" v-model="login" required />
            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Your password" v-model="password" required />
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
                login: "",
                password: "",
                mfa_code: "",
                error_message: "",
                is_mfa_enabled: false,
                is_loading: false,
            }
        },
        methods: {
            executeLogin: function(e) {
                e.preventDefault();
                var _this = this;

                if (_this.is_loading) {
                    return;
                }

                _this.is_loading = true;

                axios.post("/auth/login", {
                    <?php echo "\"" . CSRF::getTokenID() . "\""; ?>: <?php echo "\"" . CSRF::getTokenValue() . "\""; ?>,
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
                    _this.is_mfa_enabled = false;
                    _this.error_message = "Incorrect login details";
                }).finally(function() {
                    _this.is_loading = false;
                });
            }
        }
    })
</script>