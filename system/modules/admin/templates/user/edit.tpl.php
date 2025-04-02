<h1>
    Edit -
    <?php echo StringSanitiser::sanitise($user["account"]["firstname"]) . " " . StringSanitiser::sanitise($user["account"]["lastname"]); ?>
</h1>

<div class="tabs">
    <div class="tab-head">
        <a href="#details">Details</a>
        <a href="#user_security_app">Security</a>
        <a href="#groups">Groups</a>
    </div>

    <div class="tab-body">
        <div id="details">
            <?php echo $userDetails; ?>
        </div>

        <div id="user_security_app">
            <user-security-component
                user_id="<?php echo $user["id"]; ?>"
                locked="<?php echo $user["security"]["is_locked"]; ?>"
                mfa_enabled="<?php echo $user["security"]["is_mfa_enabled"]; ?>"
                pw_min_length="<?php echo Config::get('auth.login.password.min_length', 8); ?>">
            >
            </user-security-component>
        </div>

        <div id="groups">
            <div class="row-fluid body panel clearfix">
                <h3>Groups</h3>
                <ul>
                    <?php foreach ($user["groups"] as $group) : ?>
                        <li>
                            <a href="<?php echo $group["url"]; ?>" target="_blank">
                                <?php echo $group["title"]; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>