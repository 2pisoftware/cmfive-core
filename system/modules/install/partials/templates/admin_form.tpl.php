<p><em><?= $msg ?></em></p>
<div class="row">
    <div class="column small-6">
        <label>First Name
        <input type="text" required=true name="fname" class='admin_firstname'
            placeholder="Your first name"
            value="<?= $admin_firstname ?>"  />
        </label>
    </div>
    <div class="column left small-6">
        <label>Last Name
        <input type="text" required=true name="lname"
            placeholder="Your last name"
            value="<?= $admin_lastname ?>"  />
        </label>
    </div>
</div>
<?php if(!empty($company_support_email)) : ?>
<div class="row switch-row">
    <div class="column">
        <div class="switch tiny round">
            <input type="radio" id="company_support_email"
                name="admin_email_type" value="company_support_email" class='admin_email_type'
                <?= strcmp($admin_email_type, "company_support_email") === 0 ? " checked='checked'" : "" ?> />
            <label for="company_support_email"></label><!-- use to make background blue //-->
        </div>
    </div>
    <div class="column left">
        <label for="company_support_email"> Company support email : <b><?= $company_support_email ?></b></label>
    </div>
</div>
<div class="row switch-row">
    <div class="column">
        <div class="switch tiny round">
            <input type="radio" id="email" class='admin_email_type email'
                name="admin_email_type" value="admin_email"
                <?= strcmp($admin_email_type, "admin_email") === 0 ? " checked='checked'" : "" ?> />
            <label for="email"></label><!-- use to make background blue //-->
        </div>
    </div>
    <div class="column left">
        <label for="email"> Other
            <small><em>eg: <span class='user_at_domain'>user</span>@<?= $domain ?></em></small></label>
    </div>
</div>
<br/>
<?php endif; ?>
<div id='edit_admin_email' class='edit_admin_email' style='display:none;'>
    <label>Email
        <input type="text" name="email"
            placeholder="Your email address, eg: 'user@<?= $domain ?>'" value="<?= $admin_email ?>" />
    </label>
</div>
<label>Username
    <input type="text" required=true name="username"
        placeholder="Username" value="<?= $admin_username ?>"  />
</label>
<label>Password
    <input type="password" required=true name="password"
        placeholder="Password" value="<?= $admin_password ?>"  />
</label>

