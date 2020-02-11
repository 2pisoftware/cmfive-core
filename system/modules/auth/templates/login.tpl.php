<div id="app">
    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
    <div v-if="is_mfa_enabled">
        <h1>MFA Enabled</h1>
    </div>
    <div v-else>
        <form @submit="validateForm">
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
                is_mfa_enabled: false,
            }
        },
        methods: {
            validateForm: function(e) {
                e.preventDefault();

                if (this.login.trim() === "" || this.password.trim() === "") {
                    return;
                }

                this.isMfaEnabled();
            },
            isMfaEnabled: function() {
                axios.get("/auth/ajax_is_mfa_enabled", {
                    params: {
                        token_id: <?php echo "\"" . CSRF::getTokenID() . "\""; ?>,
                        token_value: <?php echo "\"" . CSRF::getTokenValue() . "\""; ?>,
                        login: this.login,
                        password: this.password,
                    }
                }).then(function(response) {
                    this.is_mfa_enabled = response.data.is_mfa_enabled;
                    console.log(response);
                }).catch(function(error) {
                    console.log(error);
                    new Toast("Login failed").show();
                });
            }
        }
    })
</script>