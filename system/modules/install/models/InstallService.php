<?php
    
include_once 'InstallStep.php';
    
class InstallService
{
    public $_steps; // will always be set, if no steps, it'll be an empty array
    
    function __construct(Web $w)
    {
        $names = $this->findSteps();
        $w->ctx("steps", $names);
        
        $this->_steps = array();
        foreach($names as $step => $name)
        {
            $this->_steps[$name] = new InstallStep($step, $name);
        }
        
    }
    
    function __init()
    {
    }
    
    function getSaved()
    {
        return $_SESSION['install']['saved'];
    }
        
    function applyDefaults(Web &$w)
    {
        if(isset($_SESSION['install']))
        {
            $w->ctx('info', 'Reset installation to defaults');
            //error_log("RESET");
            unset($_SESSION['install']);
        }
        
        // initialise the config file
        ConfigService::initConfigFile();
        
        // unset the user id to make this a fresh session
        if (!empty($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
        
        // create our session variables for the install to avoid nulls
        $_SESSION['install'] = array();
        $_SESSION['install']['default'] = array();
        $_SESSION['install']['saved'] = array();
        $_SESSION['install']['steps'] = array(); // track completion
        
        // when did this installation start?
        $_SESSION['install']['start'] = time();
        
        // encode the config to json then decode it back into an array
        $defaults = Config::get('install.default');
        
        // make sure array structure exists and set defaults
        if(isset($defaults))
        {
            foreach($defaults as $key => $value)
            {
                // save to session
                $_SESSION['install']['default'][$key] = $_SESSION['install']['saved'][$key] = self::changeVal($value);
            }
        }
        
        // defaults upon defaults to avoid nulls
        foreach($this->_steps as $name => $installStep)
        {
            $i = $installStep->getStep();
            $_SESSION['install']['steps'][$i] = array('status' => -2, // -1 not run, 0 _POST failed, current time _POST succeeded
                                                      'name' => $name,
                                                      'tests' => array(),
                                                      'errors' => array(),
                                                      'warnings' => array());
            
        }
    }
    
    function createValidations(Web &$w)
    {
        foreach($this->_steps as $name => $installStep)
        {
            $installStep->createValidations($w);
        }
    }
    
    function getSteps()
    {
        return $this->_steps;
    }
    
    /** 
     returns the install step object
     can handle retrieving via a step number or name
     WARNING CAN RETURN NULL 
     **/
    function getInstallStep($id)
    {
        if(is_int($id))
        {
            if($id > 0 && $id <= count($this->_steps))
            {
                $slice = array_slice($this->_steps, $id-1, 1);
                $installStep = array_shift($slice);
                if(isset($installStep))
                {
                    return $installStep;
                }
            }
        }
        else if(is_string($id))
        {
            if(isset($this->_steps[$id]))
            {
                return $this->_steps[$id];
            }
            
            if(isset($this->_ajax[$id]))
            {
                return $this->_ajax[$id];
            }
        }
        
        return null;
    }

    /** pass on the install function **/
    function saveInstall($id, $fields=array())
    {
        $step = $this->getInstallStep($id);
        if(isset($step))
        {
            $step->saveInstall($fields);
        }
    }
    
    /** 
     given the name of the step
     clear the errors that have previously occured 
     **/
    function clearInstallErrors($id)
    {
        $step = $this->getInstallStep($id);
        if(isset($step))
        {
            $step->clearInstallErrors();
        }
    }
    
    function formatErrors($id, $type='errors') // or 'warnings'
    {
        $step = $this->getInstallStep($id);
        if(isset($step))
        {
            return $step->formatErrors($type);
        }
        
        return '';
    }
    
    /** check all steps for the test **/
    function isPassed($test)
    {
        foreach($this->_steps as $step) {
            if($step->isPassed($test)) {
                return true;
            }
        }
        
        return false;
    }
    
    /** check all steps for the test **/
    function isPassedRecently($test)
    {
        foreach($this->_steps as $step) {
            if($step->isPassedRecently($test)){ // number of seconds
                return true;
            }
        }
        
        return false;
    }
    
    /**
     finds the step name when given the number
     returns empty string if step does not exist
     **/
    function findInstallStepName($id)
    {
        $step = $this->getInstallStep($id);
        if(isset($step))
        {
            return $step->getStepName();
        }
        
        return '';
    }
    
    /** 
     finds the step number when given the name
     returns -1 if step does not exist 
     **/
    function findInstallStep($id)
    {
        $step = $this->getInstallStep($id);
        if(isset($step))
        {
            return $step->getStep();
        }
        
        return -1;
    }
    
    function findSteps()
    {
        $install_dir = SYSTEM_PATH . DS . 'modules' . DS . 'install' . DS;
        $actions_dir = $install_dir . DS . "actions" . DS;
        $template_dir = $install_dir . DS . "templates";
        
        // dynamically generate the steps, in order, based on which php template files exist
        $steps = array();
        try
        {
            if(@$dh = opendir($template_dir)) {
                while(($file=readdir($dh)) !== false) {
                    if(preg_match("/^([0-9]+)\-(.+)\.tpl\.php$/", $file, $matches))
                    {
                        $steps[$matches[1]] = $matches[2];
                        // include each step
                        // get, post and var methods are called when required, if they exist
                        $action_file = $actions_dir . $matches[2] . ".php";
                        if(file_exists($action_file))
                        {
                            include_once($action_file);
                        }
                    }
                }
                closedir($dh);
            }
        }
        catch(Exception $e){}
        
        ksort($steps);
        return $steps;
    }
    
    /** utilities **/
    
    static function changeVal($value)
    {
        if(is_string($value))
        {
            if(preg_match("/^\-?[0-9]+\.?[0-9]*$/", $value))
                $value = floatval($value);
            
            if(strcmp(strtolower($value), 'true') === 0)
                $value = true;
            
            if(strcmp(strtolower($value), 'false') === 0)
                $value = false;
        }
        
        return $value;
    }
    
    static function getRandomString($num)
    {
        $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                          .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                          .'0123456789'); // and any other characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, $num) as $k)
        {
            $rand .= $seed[$k];
        }
        return $rand;
    }
}
   