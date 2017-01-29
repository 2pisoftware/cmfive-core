<input type='hidden' name='is_create_admin' id='is_create_admin_hidden' value='true' />
<?php
    $company_url = $_SESSION['install']['saved']['company_url'];
    if(!empty($company_url))
        $company_domain = parse_url($company_url, PHP_URL_HOST);
?>
<fieldset id="admin_user">
<?php if(isset($admins) && !empty($admins)): ?>
    <legend>Current Admins Users</legend>
    <p><em>The following admins already exist within the CmFive database.
        If you do not have access to any of these accounts it is recommended
        to create a new admin user.</em></p>
    <div id='current_admins'>
        <ul>
            <?php foreach($admins as $admin): ?>
                <li>
                    <?php echo "<b>" . $admin['login'] . "</b> : " .
                        $admin['firstname'] . " " . $admin['lastname'] . ", " . $admin['email']; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="row switch-row">
        <div class="column">
            <div class="switch tiny round">
                <input type="checkbox" id="is_create_admin" />
                <label for="is_create_admin"></label><!-- use to make background blue //-->
            </div>
        </div>
        <div class="column">
            <label for="is_create_admin" id="is_create_admin_label"><b> Or... Create new admin user</b></label>
        </div>
    </div>
    <div class='admin_form' id='admin_new' style='display:none;'>
        <fieldset>
            <?php echo $w->partial('admin_form', array('msg' => "This will create another admin user in <b>\"" .
                $_SESSION['install']['saved']['db_database'] .
                "\"</b> and assign them full priviledges. After logging into this new account, upon completion " .
                "of cmfive's installation, the system can be managed and new users created with specific " .
                "priveledges. Admin users should, however, represent, real people."), 'install'); ?>
        </fieldset>
    </div>
<?php else: ?>
    <legend>Create New Admin User</legend>
    <?php echo $w->partial('admin_form', null, 'install'); ?>
<?php endif; ?>

    <input type='hidden' name='admin_firstname'  value="<?= $_SESSION['install']['saved']['admin_firstname'] ?>" />
    <input type='hidden' name='admin_lastname'   value="<?= $_SESSION['install']['saved']['admin_lastname'] ?>" />
    <input type='hidden' name='admin_email_type' value="<?= $_SESSION['install']['saved']['admin_email_type'] ?>" />
    <input type='hidden' name='admin_email'      value="<?= $_SESSION['install']['saved']['admin_email'] ?>" />
    <input type='hidden' name='admin_username'   value="<?= $_SESSION['install']['saved']['admin_username'] ?>" />
    <input type='hidden' name='admin_password'   value="<?= $_SESSION['install']['saved']['admin_password'] ?>" />
    <br/>
    <button class="button" type="button" id='create_admin'>Next</button>
    <?php echo $w->partial('skip', array('skip' => $step+1), 'install'); ?>
</fieldset>

<script type="text/javascript">
<!--

jQuery(document).ready(function($){
                       
    $('#create_admin').click(create_admin_user);
    $('#admin_new .admin_firstname').change(change_email_placeholder);
    $('input[name=admin_email_type]').change(toggle_admin_email);
    $('#is_create_admin').change(toggle_form);
    toggle_form();
    toggle_admin_email();

    // only change email addresses for new
    function change_email_placeholder()
    {
        var name = $('#admin_new .admin_firstname');
        if(name.length)
        {
            var user = name.val().toLowerCase();
            var email = '';

            if(name.length)
            {
                email = user + "@<?= $company_domain ?>";
            }

            $('#admin_new .admin_email').attr('placeholder', "User email address" + (email.length ? ", eg: " + email : ""));
        }
    }
                       
    function toggle_form()
    {
        // there are no current admins, therefore creating a new admin is not optional
        if(!$('#is_create_admin').length) return;
                       
        var create = $('#is_create_admin:checked').length > 0;
        $('#is_create_admin_hidden').val(create);

        if(create)
        {
            // all open forms slide up
            $('#admin_new').slideDown();
        }
        else
        {
            // slide down the one that we want
            $('#admin_new').slideUp();
        }
    }


    function toggle_admin_email()
    {
        var other = $('#email:checked').length > 0;
                       
        // grey everything out using a css class
        if(other)
        {
            $('#edit_admin_email').slideDown();
        }
        else
        {
            $('#edit_admin_email').slideUp();
        }

        change_email_placeholder();

        // plus disable the actual field
        $('#edit_admin_email input').attr('disabled', !other);
    }
                       
    function create_admin_user()
    {
        // solves an issue when creating an admin is not optional
        // The hidden field input[name=is_create_admin] has an id #is_create_admin_hidden
        // 1) There are no admins, therefore, #is_create_admin does not exist performin ajax is therefore mandatory
        // 2) the checkbox #is_create_admin has to be selected
                // which exposes the form
                // and changes the value of #is_create_admin_hidden to 'true' or 'false'
        // note that when retrieving .val() the returned value is a string
                       
        if($('#is_create_admin').length && $('input[name=is_create_admin]').val() == 'false') // value is string!?1?
        {
            $('form').submit(); // just accept things as they are
        }
        else
        {
            CmFiveAjax.performAjax('admin'); // create a new admin
        }
    }
});

function ajax_admin(result)
{
    if(result['success'])
    {
        $('form').submit();
    }
}

//-->
</script>
