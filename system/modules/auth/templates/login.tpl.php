<form method="POST" action="/auth/login">
    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />

    <label for="login">Login</label>
    <input id="login" name="login" type="text" placeholder="Your login" />
    <label for="password">Password</label>
    <input id="password" name="password" type="password" placeholder="Your password" />
    <button type="submit" class="button medium-5 small-12">Login</button>
    <a onclick="window.location.href='/auth/forgotpassword';" class="medium-5 small-12 right text-right">Forgot your password?</a>
</form>
