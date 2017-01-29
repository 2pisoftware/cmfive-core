<fieldset>
    <legend>Email configuration</legend>
    <label>Mail Transport Agent (MTA)</label>
    <div class="row switch-row">
        <div class="column">
        <div class="switch tiny round">
        <input type="radio" required="true" id="email_default_mta" name="email_transport" value=""
        <?= empty($_SESSION['install']['saved']['email_transport']) ? " checked='checked'" : "" ?> />
        <label for="email_default_mta"></label><!-- use to make background blue //-->
        </div>
        </div>
        <div class="column">
        <label for="email_default_mta"> Default MTA on localhost</label>
        </div>
    </div>
    <div class="row switch-row">
        <div class="column">
        <div class="switch tiny round">
        <input type="radio" required="true" id="email_sendmail_mta" name="email_transport" value="sendmail"
        <?= strcmp($_SESSION['install']['saved']['email_transport'], "sendmail") === 0 ? " checked='checked'" : "" ?> />
        <label for="email_sendmail_mta"></label><!-- use to make background blue //-->
        </div>
        </div>
        <div class="column">
            <label for="email_sendmail_mta"> Use sendmail on localhost</label>
        </div>
    </div>
    <div class="row switch-row">
        <div class="column">
        <div class="switch tiny round">
        <input type="radio" required="true" id="email_smtp_mta" name="email_transport" value="smtp"
        <?= strcmp($_SESSION['install']['saved']['email_transport'], "smtp") === 0 ? " checked='checked'" : "" ?> />
        <label for="email_smtp_mta"></label><!-- use to make background blue //-->
        </div>
        </div>
        <div class="column">
        <label for="email_smtp_mta"> External SMTP Server</label>
        </div>
    </div>
    <br/>
    <div id='transport_sendmail' style='display:none;'>
        <label>Path to sendmail <small><em>eg: '/usr/sbin/sendmail -bs'</em></small>
            <input type="text" name="email_sendmail"
                placeholder="Path to sendmail program, eg: '/usr/sbin/sendmail -bs'"
                value="<?= $_SESSION['install']['saved']['email_sendmail'] ?>" />
        </label>
    </div>
    <div id='transport_smtp' style='display:none;'>
        <label>SMTP Host <small><em>eg: <?= $_SESSION['install']['default']['email_smtp_host'] ?></em></small>
            <input type="text" name="email_smtp_host"
                placeholder="SMTP Host, eg: <?= $_SESSION['install']['default']['email_smtp_host'] ?>"
                value="<?= $_SESSION['install']['saved']['email_smtp_host'] ?>" />
        </label>
        <label>SMPT Port <small><em>eg: <?= $_SESSION['install']['default']['email_smtp_port'] ?></em></small>
            <input type="text" name="email_smtp_port"
                placeholder="SMTP Port, eg: <?= $_SESSION['install']['default']['email_smtp_port'] ?>"
                value="<?= $_SESSION['install']['saved']['email_smtp_port'] ?>" />
        </label>
    </div>
    <label>Does <span id='transport_encryption_text'>this SMTP server</span> use Encryption?</label>
    <div class="row switch-row">
        <div class="column">
        <div class="switch tiny round">
        <input type="radio" required="true" id="encryption_none" name="email_encryption" value=""
        <?= empty($_SESSION['install']['saved']['email_encryption']) ? " checked='checked'" : "" ?> />
        <label for="encryption_none"></label><!-- use to make background blue //-->
        </div>
        </div>
        <div class="column">
            <label for="encryption_none"> No Encryption</label>
        </div>
    </div>
    <div class="row switch-row">
        <div class="column">
            <div class="switch tiny round">
                <input type="radio" required="true" id="encryption_ssl" name="email_encryption" value="ssl"
                    <?= strcmp($_SESSION['install']['saved']['email_encryption'], "ssl") === 0 ? " checked='checked'" : "" ?> />
                <label for="encryption_ssl"></label><!-- use to make background blue //-->
            </div>
        </div>
        <div class="column">
            <label for="encryption_ssl"> Secure Sockets Layer (SSL)</label>
        </div>
    </div>
    <div class="row switch-row">
        <div class="column">
            <div class="switch tiny round">
            <input type="radio" required="true" id="encryption_tls" name="email_encryption" value="tls"
            <?= strcmp($_SESSION['install']['saved']['email_encryption'], "tls") === 0 ? " checked='checked'" : "" ?> />
            <label for="encryption_tls"></label><!-- use to make background blue //-->
            </div>
        </div>
        <div class="column">
            <label for="encryption_tls"> Transport Layer Security (TLS)</label>
        </div>
    </div>
    <br/>
    <label>Does <span id='transport_authentication_text'>this SMTP server</span> require authentication?</label>
    <div class="row switch-row">
        <div class="column">
            <div class="switch tiny round">
                <input type="hidden" id="email_auth_hidden" name="email_auth"
                    value="<?= $_SESSION['install']['saved']['email_auth'] ?>" />
                <input type="checkbox" id="email_auth"
                    value="true" <?= $_SESSION['install']['saved']['email_auth'] ? " checked='checked'" : "" ?> />
                <label for="email_auth"></label><!-- use to make background blue //-->
            </div>
        </div>
        <div class="column">
            <label for="email_auth" id="email_auth_label">
                <span><?= $_SESSION['install']['saved']['email_auth'] ? "Use" : "No" ?></span> Authentication
            </label>
        </div>
    </div>
    <fieldset id="email_auth_settings">
    <legend>Authentication</legend>
        <label>Username
            <input type="text" name="email_username"
                placeholder="Username"
                value="<?= $_SESSION['install']['saved']['email_username'] ?>" />
        </label>
        <label>Password</label>
            <input type="password" name="email_password"
                placeholder="Password"
                value="<?= $_SESSION['install']['saved']['email_password'] ?>" />
        </label>
    </fieldset>
    <label>Send test email</label>
    <div class="row switch-row">
        <div class="column">
            <div class="switch tiny round">
                <input type="radio" required="true" id="email_test_no" name="email_test_send" value="no"
                    <?= strcmp($_SESSION['install']['saved']['email_test_send'], "no") === 0 ||
                        (strcmp($_SESSION['install']['saved']['email_test_send'], "company_support_email") === 0 &&
                            empty($_SESSION['install']['saved']['company_support_email'])) ||
                        (strcmp($_SESSION['install']['saved']['email_test_send'], "admin_email") === 0 &&
                            empty($_SESSION['install']['saved']['admin_email'])) ? " checked='checked'" : "" ?> />
                <label for="email_test_no"></label><!-- use to make background blue //-->
            </div>
        </div>
        <div class="column">
            <label for="email_test_no"> No</label>
        </div>
    </div>
<?php if(!empty($_SESSION['install']['saved']['company_support_email'])) : ?>
    <div class="row switch-row">
        <div class="column">
            <div class="switch tiny round">
                <input type="radio" required="true" id="email_test_company" name="email_test_send" value="company_support_email"
                    <?= strcmp($_SESSION['install']['saved']['email_test_send'], "company_support_email") === 0 ? " checked='checked'" : "" ?> />
                <label for="email_test_company"></label><!-- use to make background blue //-->
            </div>
        </div>
        <div class="column">
            <label for="email_test_company">  Use company support email :
                <?= $_SESSION['install']['saved']['company_support_email'] ?></label>
        </div>
    </div>
<?php endif; ?>
<?php if(!empty($_SESSION['install']['saved']['admin_email']) &&
         (empty($_SESSION['install']['saved']['company_support_email']) ||
          strcmp($_SESSION['install']['saved']['company_support_email'], $_SESSION['install']['saved']['admin_email']) !== 0)) : ?>
    <div class="row switch-row">
        <div class="column">
            <div class="switch tiny round">
                <input type="radio" required="true" id="email_test_admin" name="email_test_send" value="admin_email"
                    <?= strcmp($_SESSION['install']['saved']['email_test_send'], "admin_email") === 0 ? " checked='checked'" : "" ?> />
                <label for="email_test_admin"></label><!-- use to make background blue //-->
            </div>
        </div>
        <div class="column">
            <label for="email_test_admin">  Use newly created admin user email :
                <?= $_SESSION['install']['saved']['admin_email'] ?></label>
        </div>
    </div>
<?php endif; ?>
    <div class="row switch-row">
        <div class="column" >
            <div class="switch tiny round">
                <input type="radio" required="true" id="email_test_other" name="email_test_send" value="email_test"
                    <?= strcmp($_SESSION['install']['saved']['email_test_send'], "email_test") === 0 ? " checked='checked'" : "" ?> />
                <label for="email_test_other"></label><!-- use to make background blue //-->
            </div>
        </div>
        <div class="column">
            <label for="email_test_other"> Other</label>
            <div id='email_test_other_div' style='display:none;'>
                <input type="text" name="email_test" style='margin-bottom:0;'
                    placeholder="An email address (your own) to send a test email"
                    value="<?= $_SESSION['install']['saved']['email_test'] ?>" />
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <button class="button" type="button" id='next'>Next</button>
    <?php echo $w->partial('skip', array('skip' => $step+1), 'install'); ?>
</fieldset>

<script type="text/javascript">
<!--

jQuery(document).ready(function($){
                       
    /******** disable or enable the authentication based on the checkbox *********/
                       
    $('#email_auth').change(handle_auth_change);
    handle_auth_change();
                       
    function handle_auth_change()
    {
        $('#email_auth_hidden').val($('#email_auth:checked').length);
                       
        // grey everything out using a css class
        if($('#email_auth:checked').length)
        {
            $('#email_auth_settings').removeClass('disabled');
            $('#email_auth_label').removeClass('disabled');
            $('#email_auth_label span').text('Use');
        }
        else
        {
            $('#email_auth_settings').addClass('disabled');
            $('#email_auth_label').addClass('disabled');
            $('#email_auth_label span').text('No');
        }
                            
        // plus disable the actual fields
        $('#email_auth_settings input').attr('disabled', !$('#email_auth:checked').length)
    }
                       
    /****** show different fields based on the transport agent **********/
                       
    $('input[name=email_transport]').change(handle_mta_change);
    handle_mta_change();
                       
    function handle_mta_change()
    {
        var mta = '';
        var transport = $('input[name=email_transport]:checked');
        if(transport.length)
            mta = transport.val();
                       
        var speed = 'slow';
                       
        // Does [ ] use encryption?
        // Does [ ] require authentication?
        var str = 'the default MTA on localhost';
            
        if(mta == "smtp")
        {
            $('#transport_sendmail').slideUp(speed, function() {
                $('#transport_smtp').slideDown(speed);
            });

            str = 'this external SMTP Server';
        }
        else if(mta == "sendmail")
        {
            $('#transport_smtp').slideUp(speed, function() {
                $('#transport_sendmail').slideDown(speed);
            });
            
            str = 'sendmail on localhost';
        }
        else
        {
            $('#transport_smtp').slideUp(speed);
            $('#transport_sendmail').slideUp(speed);
        }

        $('#transport_encryption_text').text(str);
        $('#transport_authentication_text').text(str);
    }

    /****** enable / disable the test email field based on the checkbox beneth it ********/
                       
    $('input[name=email_test_send]').change(handle_test_change);
    handle_test_change();
                       
    function handle_test_change()
    {
        var test = $('input[name=email_test_send]:checked');
        var val = '';
        if(test.length)
            val = test.val();
                       
        if(val == "email_test") // show other
            $('#email_test_other_div').slideDown();
        else
            $('#email_test_other_div').slideUp();
    }
                                              
    $('#next').click(function(){
        var test = $('input[name=email_test_send]:checked');
        var val = '';
        if(test.length)
            val = test.val();
                     
        if(val == "no")
            $('form').submit();
        else
        {
            // TODO
            // show whirly-gig "please wait" while sending the test email ???
            // can take up to 30 seconds
            CmFiveAjax.performAjax('email');
        }
    });
});

function ajax_email(result)
{
    if(result['success'])
    {
        $('form').submit();
    }
}

//-->
</script>