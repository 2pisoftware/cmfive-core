<?php

class ValidationService
{
    public $_defaultValue = '';
    public $_minimumValue;
    public $_maximumValue;
    public $_minimumLength;
    public $_maximumLength;
    public $_fieldName;
    public $_required = false;
    public $_type = 'string';
    public $_post;
    public $_options;
    public $_step;
    public $_stepName;
    public $_ignore = false;

    function __construct()
    {
    }
    
    function create($fieldName)
    {
        $this->_fieldName = $fieldName;
        $this->_post = $fieldName; // pull from post and save as field name
        if(isset($_SESSION['install']['default'][$this->_fieldName]))
        {
            $this->_defaultValue = $_SESSION['install']['default'][$this->_fieldName];
        }
        return $this;
    }
    
    function dump()
    {
        error_log('*** fieldName: ' . $this->_fieldName .
                  ', post: ' . $this->_post .
                  ', type: ' . $this->_type .
                  ', defaultValue: ' . $this->_defaultValue .
                  ', minimumValue: ' . $this->_minimumValue .
                  ', maximumValue: ' . $this->_maximumValue .
                  ', minimumLength: ' . $this->_minimumLength .
                  ', maximumLength: ' . $this->_maximumLength .
                  ', options: ' . print_r($this->_options, true) .
                  ', required: ' . ($this->_required ? 'true' : ' false'));
    }
    
    static function add($fieldName)
    {
        $service = new ValidationService();
        return $service->create($fieldName);
    }
    
    /** daisy chaining functions **/
    
    function min($min)
    {
        $this->_minimumValue = $min;
        return $this;
    }
    
    function max($max)
    {
        $this->_maximumValue = $max;
        return $this;
    }
    
    function minLength($min)
    {
        $this->_minimumLength = $min;
        return $this;
    }
    
    function maxLength($max)
    {
        $this->_maximumLength = $max;
        return $this;
    }
    
    function required($required=true)
    {
        $this->_required = $required;
        return $this;
    }
    
    function ignore($ignore=true)
    {
        $this->_ignore = $ignore;
        return $this;
    }
   
    function options($options)
    {
        $this->_options = $options;
        return $this;
    }
    
    function type($type='string')
    {
        $this->_type = $type;
        return $this;
    }
    
    function defaultValue($defaultValue='')
    {
        $this->_defaultValue = $defaultValue;
        return $this;
    }
    
    function postName($post)
    {
        $this->_post = $post;
        return $this;
    }
    
    function step($step)
    {
        $this->_step = $step;
        return $this;
    }
    
    function stepName($name)
    {
        $this->_stepName = $name;
        return $this;
    }
    
    /** fetching fields **/
    
    function isRequired()
    {
        return $this->_required;
    }
    
    function isIgnore()
    {
        return $this->_ignore;
    }
    
    function getFieldName()
    {
        return $this->_fieldName;
    }
    
    function getDefaultValue()
    {
        return $this->_defaultValue;
    }
    
    function getType()
    {
        return $this->_type;
    }
    
    /*** static utility functions ***/
    
    // do not call directly
    // error checking assumed
    function save($val)
    {
        if(empty($val) && $this->_required)
        {
            throw new Exception('"' . $this->_fieldName . '" is a required field and cannot be empty.');
        }
        else
        {
            $_SESSION['install']['saved'][$this->_fieldName] = $val;
        }
    }
    
    function retrieve()
    {
        //error_log("POST " . $this->_fieldName . ' ' . $this->_post);
        if(!isset($_POST[$this->_post]))
        {
            throw new Exception('"' . $this->_post . '" field was not received.');
        }
        else
        {
            return $_POST[$this->_post];
        }
    }

    
    /************* DEFAULT VALIDATION *****************/
    
    function validate_string($str)
    {
        $str = trim(strip_tags($str));
        return $this->checkMaxMinLength($str);
    }
    
    function checkOptions($val)
    {
        if(isset($this->_options) && is_array($this->_options))
        {
            // make sure option is a number
            if(!in_array($val, $this->_options))
            {
                throw new Exception('"' . $val . '" is not one of the allowed options: ' . implode(', ', $this->_options));
            }
        }
        
        return $val;
    }
    
    function checkMaxMinLength($val)
    {
        if(isset($this->_minimumLength)) // min
        {
            // make sure option is a number
            if(strlen($val) < $this->_minimumLength)
            {
                throw new Exception('"' . $val . '" is below minimum required length of ' . $this->_minimumLength);
                return $val;
            }
        }
        
        if(isset($this->_maximumLength)) // max
        {
            if(strlen($val) > $this->_maximumLength)
            {
                throw new ErrorException('"' . $val . '" is longer than maximum length of ' . $this->_maximumLength, 5, E_WARNING);
                return substr($val, 0, $this->_maximumLength);
            }
        }
        
        return $val;
    }
    
    /************* CUSTOM VALIDATION ******************/
    
    function validate_url($str)
    {
        $str = $this->validate_string($str);
        
        // don't proceed on an email string
        if(empty($str)) return $str;
        
        if(strcmp('localhost', strtolower($str)) === 0)
        {
            return $str;
        }
        
        // urls can now contain unicode!!! but that unicode is written with % symbols
        
        /*
         eg: this is valid
         
         http://www.amazon.co.jp/%E3%82%A8%E3%83%AC%E3%82%AF%E3%83%88%E3%83%AD%E3%83%8B%E3%82%AF%E3%82%B9-%E3%83%87%E3%82%B8%E3%82%BF%E3%83%AB%E3%82%AB%E3%83%A1%E3%83%A9-%E3%83%9D%E3%83%BC%E3%82%BF%E3%83%96%E3%83%AB%E3%82%AA%E3%83%BC%E3%83%87%E3%82%A3%E3%82%AA/b/ref=topnav_storetab_e?ie=UTF8&node=3210981
         */
        
        $regex = "/^(https?:\/\/)?". // http or https - can be missing entirely
        "([a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9]?\.[a-zA-Z0-9][a-zA-Z0-9\-\.]*[a-zA-Z0-9])". // subdomain and domain
        "([A-Za-z0-9\/\?\-\.%_~:#\[\]@!\$&'\(\)\*\+,;=]*)$/"; // path and query string
        
        if(!preg_match($regex, $str, $matches))
        {
            throw new ErrorException('"' . $str . '" is not a valid URL.', 4, E_WARNING);
        }
        
        if(strpos($str, "..") !== false) // two dots next to each other is also bad
        {
            throw new ErrorException('"' . $str . '" is not a valid URL.', 5, E_WARNING);
        }
        
        // Cannot assume that a URI given will be either http/https, e.g. the hostname given in the
        // database setup is on whatever port and external mysql hosts can not be contacted just
        // by pinging port 80!

        // if(empty($matches[1])) // http or https ... if none? assume http
        // {
        //     $str = "http://" . $str;
        // }
        
        // $code = $this->is_200($str);
        // if($code !== 200)
        // {
        //     throw new ErrorException('"' . $str . '" was unreachable.' .
        //                              ($code !== false ? ' Returned code : ' . $code : ''), 8, E_WARNING);
        // }
        
        return $str;
    }
    
    
    // http://stackoverflow.com/questions/7952977/php-check-if-url-and-a-file-exists
    /*
     <b>Warning</b>:  file_get_contents(http://coffeecakecomputers.pro.clan.dalziel.net.ausomething): failed to open stream: php_network_getaddresses: getaddrinfo failed: nodename nor servname provided, or not known in <b>/Users/mandy/workspace/plugins/trunk/gimme-calendar-feeds.php</b> on line <b>627</b><br />
     */
    function is_200($url, $redirects=0)
    {
        $options['http'] = array(
                                 'method' => "HEAD",
                                 'ignore_errors' => 1,
                                 'max_redirects' => $redirects
                                 );
        
        try
        {
            $body = @file_get_contents($url, NULL, stream_context_create($options));
            sscanf($http_response_header[0], 'HTTP/%*d.%*d %d', $code);
        }
        catch(Exception $e)
        {
            return false;
        }
        
        return $code;
    }
    
    function validate_email($str)
    {
        $str = $this->validate_string($str);
        
        // don't proceed on an email string
        if(empty($str)) return $str;
        
        //! # $ % & ' * + - / = ? ^ _ ` { | } ~
        $regex = "/^(.+)+@([^@]+)$/"; // very basic
        
        if(!preg_match($regex, $str, $matches))
        {
            throw new Exception('"' . $str . '" is not a valid email address.');
        }
        
        return $str;
    }
    
    /** returns defaultValue **/
    function validate_float($str)
    {
        if(empty($str))
        {
            return $this->_defaultValue;
        }
        
        try
        {
            return floatval($str);
        }
        catch(Exception $e)
        {
            throw new Exception('"' . $str . '" is not formatted correctly.');
            return $this->_defaultValue;
        }
        
        return $this->checkMaxMin($val);
    }
    
    /** returns defaultValue **/
    function validate_integer($str)
    {
        if(empty($str))
        {
            return $this->_defaultValue;
        }
        
        try
        {
            $val = intval($str);
        }
        catch(Exception $e)
        {
            throw new Exception('"' . $str . '" is not formatted correctly.');
            return $this->_defaultValue;
        }
        
        return $this->checkMaxMin($val);
    }
    
    /** returns defaultValue **/
    function checkMaxMin($val)
    {
        if(isset($this->_minimumValue)) // min
        {
            // make sure option is a number
            if($val < $this->_minimumValue)
            {
                throw new Exception('"' . $str . '" is below minimum required value of ' . $this->_minimumValue);
                return $this->_defaultValue;
            }
        }
        
        if(isset($this->_maximumValue)) // max
        {
            // make sure option is a number
            if($val > $this->_maximumValue)
            {
                throw new Exception('"' . $str . '" is above maximum allowed value of ' . $this->_maximumValue);
                return $this->_defaultValue;
            }
        }
        
        return $val;
    }
    
    function validate_boolean($str)
    {
        if(empty($str))
        {
            return $this->_defaultValue;
        }
        
        try
        {
        	if(is_string($str)) {
                if(is_numeric($str)) {
                    return intval($str) > 0;
                }
        		return strcmp(strtolower($str), 'true') === 0;
        	} else {
        		throw new Exception();
        	}
            
            // not available in PHP 5.4
            //return boolval($str);
        }
        catch(Exception $e)
        {
            throw new Exception('"' . $str . '" is not formatted correctly.');
        }
        
        return $this->_defaultValue;
    }
}
