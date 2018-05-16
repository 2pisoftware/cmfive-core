<form method="POST" action="/auth/login">
    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />

    <label for="login">Login</label>
    <input id="login" name="login" type="text" placeholder="Your login" />
    <label for="password">Password</label>
    <input id="password" name="password" type="password" placeholder="Your password" />

    <label for="two_fa">2-Factor Authentication</label>
    <input id="two_fa" name="two_fa" type="text" placeholder="Your 2FA code" />

    <button type="submit" class="button large-5 small-12">Login</button>
    <button type="button" onclick="window.location.href='/auth/forgotpassword';" class="button alert large-5 small-12 right">Forgot Password</button>
</form>
