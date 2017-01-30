<form method="POST" action="/auth/login">
    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />

    <label for="login"><?php _e('Login'); ?></label>
    <input id="login" name="login" type="text" placeholder="<?php _e('Your login'); ?>" />
    <label for="password"><?php _e('Password'); ?></label>
    <input id="password" name="password" type="password" placeholder="Your password" />
    <button type="submit" class="button large-5 small-12"><?php _e('Login'); ?></button>
    <button type="button" onclick="window.location.href='/auth/forgotpassword';" class="button alert large-5 small-12 right"><?php _e('Forgot Password'); ?></button>
</form>
