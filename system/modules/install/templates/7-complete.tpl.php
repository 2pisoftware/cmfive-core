<fieldset>
    <legend>Complete installation</legend>
    <h4>Configuration is almost complete!</h4>
    <br/>
    <p>Installation started at <?= date('g:ia, jS F Y', $_SESSION['install']['start']); ?>
    <?php if(strcmp($w->Install->getInstallStep('timezone')->getStatusAsString(), 'missing') === 0) : ?>
        <br/><i class='error'>NB: The above timezone is most likely to be incorrect, as you have not set the timezone</i>
    <?php endif; ?>
    </p>
    <ol id='install_complete'>
<?php
    //$complete = $w->Install->getInstallStep('complete');
    $installSteps = $w->Install->getSteps();
    if(isset($installSteps))
    {
        foreach($installSteps as $installStep)
        {
            $s = $installStep->getStep();
            $name = $installStep->getStepName();
            
            // the 'complete' step won't have validations
            $validations = $installStep->getValidations();
            if(!empty($validations))
            {
                //error_log("validations : " . print_r($validations, true));
                foreach($validations as $fieldName => $obj)
                {
                    //error_log($fieldName . " " . ($obj->isRequired() ? "required" : "optional") . " " .
                    //          ($obj->isIgnore() ? "ignored" : "acknowledge") . " " .
                    //          (empty($_SESSION['install']['saved'][$fieldName]) ? "empty" : "valued"));
                    
                    if($obj->isRequired() && !$obj->isIgnore())
                    {
                        if(empty($_SESSION['install']['saved'][$fieldName])) {
                            $installStep->addError("&quot;" . $fieldName . "&quot; is a required field");
                        }
                    }
                }
                
                // need to run before finding out if everything is ok or not
                $html = $w->partial($name . '_summary', null, 'install');

                echo "<li step='$s' class='step " . $installStep->getStatusAsString() . "'>" .
                        "<b>" . ucwords(str_replace('-', ' ', $name)) . ":</b> " . $html .
                     "</li>";
            }
        }
    }
?>
    </ol>
    <br/>
<?php
    
/** display error messages in boxes **/
if(isset($installSteps))
{
    $hasWarnings = false;
    foreach($installSteps as $installStep)
    {
        $warnings = $installStep->formatErrors('warnings', '</li>\n<li>', '<li>', '</li>');
        if(!empty($warnings))
        {
            if(!$hasWarnings)
                echo "<div class='warning_box'>\n<h3>Warnings</h3>\n<ul>\n";
            
            echo $warnings;
        
            $hasWarnings = true;
        }
    }
    
    if($hasWarnings)
        echo "</ul>\n</div>\n<br/>\n";

    $hasErrors = false;
    foreach($installSteps as $installStep)
    {
        $errors = $installStep->formatErrors('errors', '</li>\n<li>', '<li>', '</li>');
        if(!empty($errors))
        {
            if(!$hasErrors)
                echo "<div class='error_box'>\n<h3>Errors</h3>\n<ul>\n";

            echo $errors;

            $hasErrors = true;
        }
    }
    
    if($hasErrors)
    {
        echo "</ul>\n</div>\n<br/>\n".
             "<p><i class='error'>Please resolve the above errors before creating the cmfive config file.<br/>\n".
             "<a id='ignoreErrors' class='cmfive-ajax-click' func='config'>Ignore Errors and Create Config File</a></i></p>\n<br/>\n";
    }
}

?>
    <button class="button cmfive-ajax-click" func='config' type="button"
        id='create_config' <?= !empty($errors) ? 'disabled' : ''; ?>>Create config file and LOGIN</button>
</fieldset>

<script type="text/javascript">
<!--

function ajax_config(result)
{
    if(result['success'])
    {
        window.location='/';
    }
}

//-->
</script>