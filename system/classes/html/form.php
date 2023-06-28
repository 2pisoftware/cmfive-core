<?php namespace Html;

class form {
    
    public $accept_charset;
    public $action;
    public $autocomplete; // HTML5 (on/off)
    public $enctype;
    public $method = "GET";
    public $name;
    public $novalidate; // HTML5 (novalidate)
    public $target; // (_blank, _self, _parent, _top)
    public $displayOverlay;
    
    public $id;
    public $_class;
    
    public function accept_charset($accept) {
        $this->accept_charset = $accept;
        return $this;
    }
    
    public function action($action) {
        $this->action = $action;
        return $this;
    }
    
    public function autocomplete($autocomplete) {
        $this->autocomplete = (!!($autocomplete) == true ? "on" : "off");
        return $this;
    }
    
    public function enctype($enctype) {
        $this->enctype = $enctype;
        return $this;
    }
    
    public function method($method) {
        if (in_array($method, array("GET", "POST", "PUT", "DELETE"))) {
            $this->method = $method;
        }
        return $this;
    }
    
    public function name($name) {
        $this->name = $name;
        return $this;
    }
    
    public function novalidate($novalidate) {
        if (!empty($novalidate)) {
            $this->novalidate = "novalidate";
        }
        return $this;
    }
    
    public function target($target) {
        if (in_array(strtolower($target), array("_blank", "_self", "_parent", "_top"))) {
            $this->target = strtolower($target);
        }
        return $this;
    }
    
    public function id($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setClass($class) {
        $this->_class = $class;
        return $this;
    }

    public function displayOverlay($displayOverlay)
    {
        $this->displayOverlay = $displayOverlay;
        return $this;
    }
    

    public function open() {
        $buffer = "";
        $buffer .= "<form ";
        if (!empty($this->accept_charset)) {
            $buffer .= "accept-charset='{$this->accept_charset}' ";
        }
        
        if (!empty($this->action)) {
            $buffer .= "action='{$this->action}' ";
        }
        
        if (!empty($this->autocomplete)) {
            $buffer .= "autocomplete='{$this->autocomplete}' ";
        }
        
        if (!empty($this->enctype)) {
            $buffer .= "enctype='{$this->enctype}' ";
        }
        
        if (!empty($this->method)) {
            $buffer .= "method='{$this->method}' ";
        }
        
        if (!empty($this->name)) {
            $buffer .= "name='{$this->name}' ";
        }
        
        if (!empty($this->novalidate)) {
            $buffer .= "novalidate='{$this->novalidate}' ";
        }
        
        if (!empty($this->target)) {
            $buffer .= "target='{$this->target}' ";
        }
        
        if (!empty($this->id)) {
            $buffer .= "id='{$this->id}' ";
        }

        if (!empty($this->_class)) {
            $buffer .= "class='{$this->_class}' ";
        }

        if (!empty($this->displayOverlay) && $this->displayOverlay === true) {
            $buffer .= " data-show-overlay ";
        }

        $buffer .= ">";
        
        // Automatically print CSRF token
        if (class_exists("CSRF") && !empty($this->method) && ($this->method == "POST")) {
            $buffer .= "<input type='hidden' name='" . \CSRF::getTokenID() . "' value='" . \CSRF::getTokenValue() . "' />";
        }
        
        return $buffer;
    }
    
    public function close($button_title = 'Save', $extrabuttons = null) {
        if (!empty($button_title)) {
            $button = new \Html\button();
            $button->type("submit")->text($button_title)->setClass('button savebutton');
            $cancel_button = new \Html\button();
            $cancel_button->setClass('button secondary cancelbutton');
            $cancel_button->type("button")->text("Cancel")->js("if($('#cmfive-modal').is(':visible')){ $('#cmfive-modal').foundation('reveal', 'close'); } else { window.history.back(); }");
                    
            return "<div class='row-fluid form-button-container'><div class='small-12 columns'>{$button->__toString()}{$cancel_button->__toString()}{$extrabuttons}</div></div></form>";
        } else {
            return "</form>";
        }
    }
}
