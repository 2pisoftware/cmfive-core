<?php
    
function index_ALL(Web $w) {
    
    // if config file already exists... do nothing
    if(is_file(ROOT_PATH.'/config.php'))
    {
        $w->Install;
        return;
    }
    
    // Did we reach here by submitting a form?
    $post_action = '';
    $post_step = -1;
    if(isset($_POST['step']) && preg_match("/^[0-9]+$/", $_POST['step']))
    {
        $post_step = intval($_POST['step']);
        $postInstallStep = $w->Install->getInstallStep($post_step);
        if(isset($postInstallStep))
        {
            $post_action = $postInstallStep->getStepName();
            $postInstallStep->clearInstallErrors();
            
            // TODO something needs to be done about old and irrelevant test results showing up.
            //$postInstallStep->clearInstallTestResults();
        }
    }
    
    if($post_step > 0 && empty($post_action))
    {
        $w->ctx('error', "No post action found for step " . $post_step);
    }

    // get the current step and its action name
    $submodule_parts = explode('-', $w->currentSubModule());
    $step = $submodule_parts[0];
    //$step_name = $submodule_parts[1];
    
    $w->ctx("step", $step);
    $get_action = $w->currentAction();
    $w->ctx("stepName", $get_action);
    
    //$installStep = $w->Install->getInstallStep($get_action);
    //if(isset($installStep))
    //{
    //    error_log("This should clear the errors for the curr")
    //    $installStep->clearInstallErrors(); // clear errors for step???
    //}
    
    //$w->ctx('info', //'<h4>steps</h4><pre>' . print_r($steps, true) . '</pre>' .
            //'<h4>JSON:</h4><pre>' . print_r($json['install'], true) . '</pre>' .
            //'<h4>CONFIG:</h4><pre>' . print_r($config, true) . '</pre>' .
            //'<h4>INSTALL:</h4><pre>' . print_r($_SESSION['install'], true) . '</pre>');// .
    //error_log('<h4>POST:</h4><pre>' . print_r($_POST, true) . '</pre>');

    
    // set the default timezone, that doesn't get set otherwise
    if(!empty($_SESSION['install']['saved']['timezone'])){
        date_default_timezone_set($_SESSION['install']['saved']['timezone']);
    }

    // set the form action variable - only if we did not reach here via ajax
    $form_action = ''; // avoid null variables
    if(!$w->isAjax())
    {
        // set up the form action for the next step
        $next_step = $step + 1;
        $nextInstallStep = $w->Install->getInstallStep($next_step);

        if(isset($nextInstallStep))
        {
            $nextInstallStep->clearInstallErrors(); // has to be cleared before partial templates are displayed
            $form_action = DS . 'install' . DS .
                            $next_step . DS . $nextInstallStep->getStepName();
        
            if(empty($form_action))
            {
                $w->ctx('error', 'Form action is not defined');
            }
        }
    }
    $w->ctx("form_action", $form_action);
    
    // if we're coming in fresh then apply defaults
    if(!isset($_SESSION['install']['start']) || !isset($_SERVER['HTTP_REFERER']) ||
       strpos($_SERVER['HTTP_REFERER'], WEBROOT) !== 0)
    {
        // new installation with defaults, including start time of installation
        $w->Install->applyDefaults($w);
    }
    
    // has to be done for all instances as it loads the validations into memory
    // rather than being saved in the session variable
    $w->Install->createValidations($w);

    /*error_log(($w->isAjax() ? "AJAX" : "NORMAL") . " " . $post_action . "_POST() " .
              (function_exists($post_action . "_POST") ? 'exists' : 'missing') .
              " POST: " . print_r($_POST, true));//*/
    
    // only called if $_POST['step'] exists - this could be an ajax call - if $_POST['step'] is defined
    if(isset($postInstallStep))
    {
        // clear errors for step???
        //if(!$w->isAjax())
        //{
        //    $postInstallStep->clearInstallErrors();
        //}
        
        // sets the variables in post of /actions folder
        // also sets the success message
        // but what if it did not succeed?
        $postInstallStep->runPost($w);
    }

    //AJAX
    if($w->isAjax() && isset($get_action) && !empty($get_action))
    {
        $response = array();
        $response['functionName'] = $get_action;
        $response['debug'] = false; // change to true to see massive info in any errors or warnings
        if($response['debug'])
        {
            $response['post'] = $_POST;
        }
        
        // if the ajax action was to reset the installation... then reset
        if(strcmp($get_action, 'reset') === 0)
        {
            $w->Install->applyDefaults($w);
            
            $response['successMsg'] = "Reset installation configuration";
            $response['success'] = true;
        }
        else
        {
            // calls the ajax function
            $w->partial("ajax_" . $get_action, $_POST, 'install');
            if(!empty($postInstallStep))
            {
                $postInstallStep->calculateStatus();
                //error_log($postInstallStep->getStatus() . " === " . INSTALLER_STEP_OK);
                //error_log(gettype($postInstallStep->getStatus()) . " === " . gettype(INSTALLER_STEP_OK));
                $response['success'] = $postInstallStep->getStatus() === INSTALLER_STEP_OK;
                //if($response['success'] && !isset($response['successMsg']))
                //    $response['successMsg'] = $get_action . " ran successfully";
                $response['warning'] = $postInstallStep->formatErrors('warnings');
                $response['error'] = $postInstallStep->formatErrors();
            }
        }
       
        //error_log("AJAX RESPONSE : " . print_r($response, true));
        echo json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

        die();
    }
    
    // called before current step's template is called
    if(!empty($get_action) && function_exists($get_action . "_GET"))
    {
        call_user_func_array($get_action . "_GET", array(&$w));
    }
    
    if(isset($postInstallStep))
    {
        // clear errors for step???
        //if(!$w->isAjax())
        //{
        //    $postInstallStep->clearInstallErrors();
        //}
        
        // sets the variables in post of /actions folder
        // also sets the success message
        // but what if it did not succeed?
        // adds it to the context variable for displaying in the template
        $postInstallStep->calculateStatus();
        $postInstallStep->displayMessages($w);
    }

    
    //$json = json_decode(Config::toJson(), true);
    //$config = Config::get('install');
    
    // avoid outputting debugging statements to screen before AJAX has run its course
    //$w->ctx('info', //'<h4>steps</h4><pre>' . print_r($steps, true) . '</pre>' .
                    //'<h4>JSON:</h4><pre>' . print_r($json['install'], true) . '</pre>' .
                    //'<h4>CONFIG:</h4><pre>' . print_r($config, true) . '</pre>' .
                //'<h4>INSTALL:</h4><pre>' . print_r($_SESSION['install']['default'], true) . '</pre>');// .
                    //'<h4>POST:</h4><pre>' . print_r($_POST, true) . '</pre>');
    
}




    
