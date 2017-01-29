<?php

define("INSTALLER_STEP_MISSING", -2);
define("INSTALLER_STEP_WARNING", -1);
define("INSTALLER_STEP_ERROR", 0);
define("INSTALLER_STEP_OK", 1);

define("INSTALLER_RECENT", 30);

class InstallStep
{
    public $_step;
    public $_stepName;
    public $_validations;
    public $_successMsg;

    function __construct($step, $stepName)
    {
        $this->_step = $step;
        $this->_stepName = $stepName;
        $this->_validations = array();
    }

    /** getters **/

    function getStep()
    {
        return $this->_step;
    }

    function getStepName()
    {
        return $this->_stepName;
    }

    function createValidations(Web &$w)
    {
        // create the variables
        if(function_exists($this->_stepName . "_VAR"))
        {
            $vars = call_user_func_array($this->_stepName . "_VAR", array(&$w));
            if(!empty($vars))
            {
                if(is_array($vars))
                {
                    foreach($vars as $obj) // ValidationService Object
                    {
                        $this->addValidation($obj);
                    }
                }
                elseif(strcmp(gettype($obj), 'ValidationService') === 0)
                {
                    $this->addValidation($obj);
                }
            }
        }
    }

    function addValidation(ValidationService $validation)
    {
        $validation->step($this->getStep())->stepName($this->getStepName());

        $this->_validations[$validation->getFieldName()] = $validation;

        $fieldName = $validation->getFieldName();
        if(!isset($_SESSION['install']['saved'][$fieldName]))
        {
            $_SESSION['install']['default'][$fieldName] = $_SESSION['install']['saved'][$fieldName] = $validation->getDefaultValue();
        }
    }

    /** WARNING this function can return null **/
    function getValidation($fieldName)
    {
        if(isset($this->_validations[$fieldName]))
        {
            return $this->_validations[$fieldName];
        }

        return null;
    }

    function getValidations()
    {
        return $this->_validations;
    }

    /** error handling **/

    function isError()
    {
        return count($_SESSION['install']['steps'][$this->_step]['errors']) > 0;
    }

    function isErrorOrWarning()
    {
        return count($_SESSION['install']['steps'][$this->_step]['errors']) > 0 ||
                count($_SESSION['install']['steps'][$this->_step]['warnings']) > 0;
    }

    function addError($error, $type='errors')
    {
        //error_log("ERRORS : " . print_r($_SESSION['install']['steps'][$this->_step], true));
        //          needle   haystack
        if(!isset($_SESSION['install']['steps'][$this->_step][$type]))
        {
            $_SESSION['install']['steps'][$this->_step][$type] = array();
        }
        
        if(!in_array($error, $_SESSION['install']['steps'][$this->_step][$type]))
        {
            $_SESSION['install']['steps'][$this->_step][$type][] = $error;
        }

        // make sure that it's not going to be ignored as ok
        $_SESSION['install']['steps'][$this->_step]['status'] =
            strcmp($type, 'errors') === 0 ? INSTALLER_STEP_ERROR : INSTALLER_STEP_WARNING;
    }

    function runPost(Web &$w)
    {
        /*echo "<pre>";
        var_dump($_SESSION);
        echo "</pre>";
        die;*/
        
        if(function_exists($this->_stepName . "_POST"))
        {
            // used for error checking and setting of the session variables
            $this->_successMsg = call_user_func_array($this->_stepName . "_POST", array(&$w));
        }
    }

    function calculateStatus($seconds=INSTALLER_RECENT)
    {
        /*echo "<pre>";
         var_dump($_SESSION);
         echo "</pre>";
         die;//*/
        
        /*error_log("CALCULATE STATUS " . count($_SESSION['install']['steps'][$this->_step]['errors']) . ' errors: ' .
             print_r($_SESSION['install']['steps'][$this->_step]['errors'], true) . " " .
             count($_SESSION['install']['steps'][$this->_step]['warnings']) . ' warnings: ' .
             print_r($_SESSION['install']['steps'][$this->_step]['warnings'], true));*/
        
        // now handle success or failure
        // include warnings, current time for a pass, 0 for fail, -1 for not run
        if(count($_SESSION['install']['steps'][$this->_step]['errors']) > 0)
        {
            $_SESSION['install']['steps'][$this->_step]['status'] = INSTALLER_STEP_ERROR;
        }
        else if(count($_SESSION['install']['steps'][$this->_step]['warnings']) > 0)
        {
            $_SESSION['install']['steps'][$this->_step]['status'] = INSTALLER_STEP_WARNING;
        }
        else
        {
            $status = INSTALLER_STEP_OK; // tentative ok
            foreach($_SESSION['install']['steps'][$this->_step]['tests'] as $name => $test)
            {
                if($test['ran'] > time() - $seconds)
                {
                    $status = $test['result'];
                }
            }
            $_SESSION['install']['steps'][$this->_step]['status'] = $status;
        }
    }

    function displayMessages(Web &$w)
    {
        if(!$w->isAjax()) // don't display success for ajax
        {
            $context = 'info';
            switch($_SESSION['install']['steps'][$this->_step]['status'])
            {
                case INSTALLER_STEP_WARNING: $context = 'warn'; break;
                case INSTALLER_STEP_ERROR: $context = 'error'; break;
            }

            $testMsg = $this->formatTests();
            //error_log("DISPLAY MESSAGES : " . $this->_step . "-" . $this->_stepName . " " .
            //            $context . " \"" . $this->_successMsg . $testMsg . "\"");
            $w->ctx($context, $this->_successMsg . $testMsg);
        }
    }

    /** test handling **/

    function ranTest($test, $result=true)
    {
        $_SESSION['install']['steps'][$this->_step]['tests'][$test]['ran'] = time();
        $_SESSION['install']['steps'][$this->_step]['tests'][$test]['result'] = $result;
    }

    function isPassed($test)
    {
        return isset($_SESSION['install']['steps'][$this->_step]['tests'][$test]) &&
                    $_SESSION['install']['steps'][$this->_step]['tests'][$test]['result'];
    }

    function isPassedRecently($test, $seconds=INSTALLER_RECENT)
    {
        return $this->isPassed($test) &&
                $_SESSION['install']['steps'][$this->_step]['tests'][$test]['ran'] > time() - $seconds;
    }

    function isFailed($test)
    {
        return isset($_SESSION['install']['steps'][$this->_step]['tests'][$test]) &&
                    $_SESSION['install']['steps'][$this->_step]['tests'][$test]['result'];
    }

    function getStatus()
    {
        try
        {
            return intval($_SESSION['install']['steps'][$this->_step]['status']);
        }
        catch(Exception $e)
        {
            return INSTALLER_STEP_ERROR;
        }
    }

    function getStatusAsString()
    {
        if(isset($_SESSION['install']['steps'][$this->_step]['status']))
        {
            $status = intval($_SESSION['install']['steps'][$this->_step]['status']);
            switch($status)
            {
                case INSTALLER_STEP_WARNING: return 'warning';
                case INSTALLER_STEP_ERROR: return 'failed';
                case INSTALLER_STEP_OK: return 'passed';
            }
        }
        return 'missing';
    }

    function getTestResultStr($test, $passed, $failed, $untested, $seconds=-1)
    {
        if(isset($_SESSION['install']['steps'][$this->_step]['tests'][$test]))
        {
            // if recent
            if($seconds < 0 || $_SESSION['install']['steps'][$this->_step]['tests'][$test]['ran'] > time() - $seconds)
            {
                if($_SESSION['install']['steps'][$this->_step]['tests'][$test]['result'])
                    return '<span class="test passed">' . $passed . '</span>';
                else
                    return '<span class="test failed">' . $failed . '</span>';
            }
        }

        return '<span class="test untested">' . $untested . '</span>';
    }

    function clearInstallErrors()
    {
        unset($_SESSION['install']['steps'][$this->_step]['errors']);
        $_SESSION['install']['steps'][$this->_step]['errors'] = array();

        unset($_SESSION['install']['steps'][$this->_step]['warnings']);
        $_SESSION['install']['steps'][$this->_step]['warnings'] = array();

        /*error_log("CLEAR INSTALL ERRORS " . count($_SESSION['install']['steps'][$this->_step]['errors']) . ' errors: ' .
                  print_r($_SESSION['install']['steps'][$this->_step]['errors'], true) . " " .
                  count($_SESSION['install']['steps'][$this->_step]['warnings']) . ' warnings: ' .
                  print_r($_SESSION['install']['steps'][$this->_step]['warnings'], true));*/
        /*error_log("[" . $this->_step . " " . $this->_stepName . "] " .
        count($_SESSION['install']['steps'][$this->_step]['errors']) . ' errors and ' .
        count($_SESSION['install']['steps'][$this->_step]['warnings']) . ' warnings');*/
    }

    function formatErrors($type='errors', $glue="<br/>\n", $before='', $after="<br/>\n") // or 'warnings'
    {
        if(!isset($this->_step) || $this->_step < 0 || $this->_step > count($_SESSION['install']['steps']))
        {
            return ''; //"<br/>\nERROR:<br/>\nCould not determine if any errors of warnings exist, step=$step<br/>\n";
        }

        if(isset($_SESSION['install']['steps'][$this->_step][$type]) && is_array($_SESSION['install']['steps'][$this->_step][$type]))
            $errors = implode($glue, $_SESSION['install']['steps'][$this->_step][$type]);
        else
            return ''; //"<br/>\nERROR:<br/>\nCould not determine if any " . $type . " exist.<br/>\n";

        if(!empty($errors))
        {
            if(empty($before))
                return strtoupper($type) . ":<br/>\n" . $errors . $after;
            return $before . $errors . $after;
        }

        return '';
    }


    function formatTests()
    {
        if(empty($_SESSION['install']['steps'][$this->_step]['tests']))
            return "";

        //$str = "<br/>\nTEST RESULTS:<br/>\n";
        $str = "<ul>\n";
        foreach($_SESSION['install']['steps'][$this->_step]['tests'] as $name => $test)
        {
            $str .= "<li>\"" . str_replace('_', ' ', $name) . "\""; //"&nbsp;&nbsp;&nbsp;&nbsp;* " . $name;
            if($test['result'] === INSTALLER_STEP_ERROR)
            {
                $str .= " <b>ERRORS</b>";
            }
            if($test['result'] === INSTALLER_STEP_WARNING)
            {
                $str .= " <b>WARNINGS</b>";
            }
            else
            {
                $str .= " <b>OK</b>";// at " . date('h:i a', $time);
            }

            $str .= "</li>\n"; //"<br/>\n";
        }
        $str .= "</ul>\n";

        return $str;
    }

    /** save the installation fields to the session after validation **/
    function saveInstall($field=array())
    {
        $objs = array();
        if(empty($field)) {
            $objs = $this->_validations;
        }
        else
        {
            if(is_array($field))
            {
                // compile a list of ValidationService objects to go through based on the vars passed in
                foreach($field as $fieldName) {
                    $objs[$fieldName] = $this->_validations[$fieldName];
                }
            }
            else if(is_string($field))
            {
                $objs[$field] = $this->_validations[$field];
            }
        }

        //error_log("OBJECTS TO SAVE: (" . print_r($field, true) . ") " . print_r($objs, true));

        // go through each ValidationService object, validate it, and save it to the $_SESSION variable
        foreach($objs as $obj)
        {
            //$obj->dump();
            if($obj->isIgnore())
                continue;

            try
            {
                $val = $obj->retrieve();
                //error_log("1. " . $val);
                $val = call_user_func_array(array($obj, "validate_" . $obj->getType()), array($val));
                //error_log("2. " . $val);
                $val = $obj->checkOptions($val);
                //error_log("3. " . $val);
                $obj->save($val);
            }
            catch(ErrorException $e)
            {
                if($e->getSeverity() == E_WARNING)
                {
                    $this->addError($e->getMessage(), 'warnings');
                }
                else
                {
                    $this->addError($e->getMessage());
                }
            }
            catch(Exception $e)
            {
                $this->addError($e->getMessage());
            }
        }
    }
}
