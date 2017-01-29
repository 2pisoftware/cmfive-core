<?php
    $example_url = $_SESSION['install']['default']['company_url'];
    $example_domain = parse_url($example_url, PHP_URL_HOST);
?>
<fieldset>
    <legend>General configuration</legend>
    <label>Application name
        <input type="text" name="application_name"
            placeholder="eg: <?= $_SESSION['install']['default']['application_name'] ?>"
            value="<?= $_SESSION['install']['saved']['application_name'] ?>" />
    </label>
    <label>Company name
        <input type="text" name="company_name"
            placeholder="eg: <?= $_SESSION['install']['default']['company_name'] ?>"
            value="<?= $_SESSION['install']['saved']['company_name'] ?>" />
    </label>
    <label>Company url <small><em>eg: <?= $example_url . ", usually not http" .
        ($_SERVER['HTTP_HOST'] ? 's' : '') . "://" . $_SERVER['HTTP_HOST'] ?></em></small>
        <input type="url" name="company_url"
            placeholder="eg: <?= $example_url ?>" id='company_url'
            value="<?= $_SESSION['install']['saved']['company_url'] ?>" />
    </label>
    <label>*Company support email <small><em>Email will appear to be sent from this email address,
                                                eg: support@<?= $example_domain ?></em></small>
        <input type="email" required=true name="company_support_email" id='company_support_email'
            placeholder="Must be valid, eg: support@<?= $example_domain ?>"
            value="<?= $_SESSION['install']['saved']['company_support_email'] ?>" />
    </label>
    <br/>
    <button class="button" type="submit">Next</button>
    <button class="button secondary cmfive-ajax-click" func='reset' type="button" id="reset_defaults">Reset</button>
    <?php echo $w->partial('skip', array('skip' => $step+1), 'install'); ?>
</fieldset>

<script type="text/javascript">
<!--
jQuery(document).ready(function($){

    $('#company_url').change(change_email_placeholder);
                       
    function change_email_placeholder()
    {
        var domain = $('#company_url').val().replace(/^https?:\/\/(www\.)?/g, "");;
        $('#company_support_email').attr('placeholder',
                                         "Company support email, eg: support@" + (domain.length ? domain : "<?= $example_domain ?>"));
    }
                       
});

//-->
</script>