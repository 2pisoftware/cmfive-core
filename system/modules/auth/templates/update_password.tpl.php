<h3 align="center">Update Password</h3>
<form method="POST" action="/auth/update_password">
    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />

    <label for="password">Password</label>
    <input id="password" name="password" type="password" placeholder="Password" required />
    <label for="confirm_password">Confirm Password</label>
    <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm Password" required />
    <button type="submit" class="button medium-5 small-12">Update</button>
</form>

