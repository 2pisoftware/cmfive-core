<div id="user_edit_app">
    <user-edit-component
        user_json='<?php echo json_encode($user); ?>'
        enforce_max_length="<?php echo Config::get('auth.login.password.enforce_length') ? 'true' : 'false' ?>"
        enforce_min_length="<?php echo Config::get('auth.login.password.min_length', 8); ?>">
    </user-edit-component>
</div>

<div class="d-none" id="userdetailsform">
    <?php echo $userDetails; ?>
</div>