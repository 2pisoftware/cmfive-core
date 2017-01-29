<fieldset>
    <legend>Database connection</legend>
    <label>Driver <small><em>(Default: <?= $_SESSION['install']['default']['db_driver'] ?>)</small></em>
    <select name="db_driver" required="true">
        <option>mysql</option>
    </select></label>
    <label>*Hostname <small><em>(Default: <?= $_SESSION['install']['default']['db_host'] ?>)</small></em>
    <input type="text" required="true" name="db_host"
        placeholder="Database Hostname, eg: <?= $_SESSION['install']['default']['db_host'] ?>"
        value="<?= $_SESSION['install']['saved']['db_host'] ?>" /></label>
    <label>Port <small><em>(Default: <?= $_SESSION['install']['default']['db_port'] ?>)</small></em>
    <input type="number" name="db_port"
        placeholder="Database Port, eg: <?= $_SESSION['install']['default']['db_port'] ?>"
        value="<?= $_SESSION['install']['saved']['db_port'] ?>"  /></label>
    <fieldset>
        <legend>Database User</legend>
        <div class="row switch-row">
            <div class="column">
                <div class="switch tiny round">
                    <input type="radio" required="true" id="db_user_type_existing" name="db_user_type" value="existing"
                        <?= strcmp($_SESSION['install']['saved']['db_user_type'], "existing") === 0 ? " checked='checked'" : "" ?> />
                    <label for="db_user_type_existing"></label><!-- use to make background blue //-->
                </div>
            </div>
            <div class="column">
                <label for="db_user_type_existing"> Existing user</label>
            </div>
        </div>
        <div class="row switch-row">
            <div class="column">
                <div class="switch tiny round">
                    <input type="radio" required="true" id="db_user_type_new" name="db_user_type" value="new"
                        <?= strcmp($_SESSION['install']['saved']['db_user_type'], "new") === 0 ? " checked='checked'" : "" ?> />
                    <label for="db_user_type_new"></label><!-- use to make background blue //-->
                </div>
            </div>
            <div class="column">
                <label for="db_user_type_new"> Create new user</label>
            </div>
        </div>
        <label>*Username <?php if(!empty($_SESSION['install']['saved']['application_name']))
            echo "<small><em>(Eg: " . strtolower($_SESSION['install']['saved']['application_name']) . ")</small></em>"; ?>
        <input type="text" required="true" name="db_username"
            placeholder="Your Database Username" required=true 
            value="<?= $_SESSION['install']['saved']['db_username'] ?>" /></label>
        <label>Password
        <input type="password" required="true" name="db_password"
            placeholder="Your Database Password"
            value="<?= $_SESSION['install']['saved']['db_password'] ?>" /></label>
        <div id='root_password_div' style='display:none;'>
            <label>Root password
            <input type="hidden" name="username" value="root" />
            <input type="password" required="true" id="db_root" autocomplete="off" name='db_root'
                placeholder="Root password is required to create a new user"
                value="" /></label>
        </div>
    </fieldset>
    <br/>
    <button class="button" type="button" id='check_connection'>Next</button>
    <?php echo $w->partial('skip', array('skip' => $step+2), 'install'); ?>
</fieldset>

<script type="text/javascript">
<!--

jQuery(document).ready(function($){
                       
    $('input[name=db_user_type]').change(userTypeChanged);
    userTypeChanged();
                       
    function userTypeChanged(e)
    {
        var showRoot = $("#db_user_type_new:checked").length;

        if(showRoot)
            $("#root_password_div").slideDown();
        else
            $("#root_password_div").slideUp();
    }
                       
    $('#check_connection').click(function(e){
                                 
        if($("#db_user_type_new:checked").length)
        {
            CmFiveAjax.performAjax('user');
        }
        else
        {
            CmFiveAjax.performAjax('connection');
        }
    });
});

// ajax callback functions have to be outside of that ajax thingo
function ajax_user(result)
{
    if(result['success'])
    {
        $('form').submit();
    }
}
                   
function ajax_connection(result)
{
    if(result['success'])
    {
        $('form').submit();
    }
}

//-->
</script>
