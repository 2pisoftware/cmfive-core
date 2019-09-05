<?php namespace Html\Form;

/**
 * A custom Html\Form element to create a captcha field. Validation is
 * the responsibility of the action receiving the form submission.
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class Captcha extends \Html\Form\FormElement {
	
    use \Html\GlobalAttributes;
    
    public $sitekey = '';

    private $_type = 'recaptcha';
    private static $_available_types = ['recaptcha'];

    /**
     * Sets the sitekey parameter
     *  
     * @param String $sitekey
     * @return this
     */
    public function setSiteKey($sitekey) {
        $this->sitekey = $sitekey;
        return $this;
    }

    /**
     * Sets the type of captcha to use.
     * 
     * Currently only supports Googles Recaptcha
     *
     * @param String $type
     * @return this
     */
    public function setType($type) {
        $type = strtolower($type);
        if (in_array($type, $_available_types)) {
            $this->_type = $type;
        }

        return $this;
    }

    /**
     * Converts class to HTML
     *
     * @return string
     */
    public function __toString() {
        switch($this->_type) {
            case 'recaptcha':
            default:
                return '<div class="g-recaptcha" data-sitekey="' . $this->sitekey . '"></div>';
        }
    }

}
