<form method="POST" action="/auth/forgotpassword">
    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
    <label for="login"><?php echo Config::get('auth.login_label', 'Login'); ?></label>
    <input id="login" name="login" type="text" placeholder="Your <?php echo Config::get('auth.login_label', 'Login'); ?>" />
    <button type="submit" class="button large-5 small-12">Submit</button>
    <button type="button" onclick='window.location.href="/auth/login"' class="button secondary large-5 small-12 right">Back</button>
</form>