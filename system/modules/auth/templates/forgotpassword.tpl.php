<form method="POST" action="/auth/forgotpassword">
    <input type="hidden" name="<?php echo CSRF::getTokenID(); ?>" value="<?php echo CSRF::getTokenValue(); ?>" />
    <div class='row'>
        <label for="login" class='col-form-label'><?php echo Config::get('auth.login_label', 'Login'); ?>
            <?php
            echo (new \Html\Form\InputField([
                "id|name" => "login",
                "placeholder" => Config::get('auth.login_label', 'Login'),
                "required" => true,
                "class" => "form-control-lg",
            ]));
            ?>
        </label>
    </div>
    <div class='row d-flex justify-content-between mt-3 row-cols-1 row-cols-sm-2'>
        <div class='col'>
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </div>
        <div class='col mt-3 mt-sm-0'>
            <button type="button" onclick='window.location.href="/auth/login"' class="btn btn-secondary w-100">Back</button>
        </div>
    </div>
</form>