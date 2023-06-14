<?php

namespace Html;

/**
 * \Html\button class for creating buttons
 * https://developer.mozilla.org/en-US/docs/Web/HTML/Element/button
 *
 * @author Adam Buckley <adam.buckley90@gmail.com>
 */
class button
{
    use GlobalAttributes;

    public $autofocus = false; // HTML5
    public $disabled = false;
    public $form; // HTML5
    public $formaction; // HTML5
    public $formenctype; // HTML5
    public $formmethod; // HTML5
    public $formnovalidate = false; // HTML5
    public $formtarget; // HTML5
    public $id;
    public $_class = "button tiny ";
    public $name;
    public $type;
    public $text;
    public $value;
    public $js;
    // Non standard button attributes
    public $confirm;
    public $newtab = false;
    public $href;
    public $onclick;

    public static function factory()
    {
        return new \Html\button();
    }

    public function __set($property, $value)
    {
        //        echo $property . " " . $value;
        if (method_exists($this, $property)) {
            $this->$property($value);
        } else {
            // Try the "setParameter" method syntax
            if (method_exists($this, "set" . ucfirst($property))) {
                $method = "set" . ucfirst($property);
                $this->$method($value);
            }
        }
        return $this;
    }

    public function __toString()
    {
        if (empty($this->js)) {
            $js = "";
            if (!empty($this->confirm)) {
                $js .= "if(confirm('" . $this->confirm . "')) {";
            }
            if (!empty($this->href)) {
                if (!$this->newtab) {
                    $js .= "parent.location='" . $this->href . "'; return false;";
                } else {
                    $js .= "window.open('" . $this->href . "', '_blank').focus(); return false;";
                }
            }
            if (!empty($this->confirm)) {
                $js .= "}";
            }
        } else {
            $js = $this->js;
        }

        $buffer = "<button ";
        if ($this->autofocus === true) {
            $buffer .= "autofocus=true ";
        }
        if ($this->disabled === true) {
            $buffer .= "disabled=true ";
        }
        if (!empty($this->form)) {
            $buffer .= "form=\"{$this->form}\" ";
        }
        if (!empty($this->formaction)) {
            $buffer .= "formaction=\"{$this->formaction}\" ";
        }
        if (!empty($this->formenctype)) {
            $buffer .= "formenctype=\"{$this->formenctype}\" ";
        }
        if (!empty($this->formmethod)) {
            $buffer .= "formmethod=\"{$this->formmethod}\" ";
        }
        if (!empty($this->formnovalidate)) {
            $buffer .= "formnovalidate=\"{$this->formnovalidate}\" ";
        }
        if (!empty($this->formtarget)) {
            $buffer .= "formtarget=\"{$this->formtarget}\" ";
        }
        if (!empty($this->id)) {
            $buffer .= "id=\"{$this->id}\" ";
        }
        if (!empty($this->_class)) {
            $buffer .= "class=\"{$this->_class}\" ";
        }
        if (!empty($this->name)) {
            $buffer .= "name=\"{$this->name}\" ";
        }
        if (!empty($this->type)) {
            $buffer .= "type=\"{$this->type}\" ";
        }
        if (!empty($this->value)) {
            $buffer .= "value=\"{$this->value}\" ";
        }
        if (!empty($this->onclick)) {
            $buffer .= "onclick=\"{$this->onclick}\"";
        } else {
            if (!empty($js)) {
                $buffer .= "onclick=\"{$js}\" ";
            }
        }
        if (!empty($this->_attributeList)) {
            foreach ($this->_attributeList as $attribute) {
                foreach ($attribute as $key => $value) {
                    if (!empty($value)) {
                        $buffer .= "{$key}=\"{$value}\" ";
                    } else {
                        $buffer .= "{$key} ";
                    }
                }
            }
        }
        $buffer .= (">{$this->text}</button>");
        return $buffer;
    }

    public function autofocus($autofocus)
    {
        $this->autofocus = (bool) $autofocus;
        return $this;
    }

    public function disabled($disabled)
    {
        $this->disabled = (bool) $disabled;
        return $this;
    }

    public function form($form)
    {
        $this->form = $form;
        return $this;
    }

    public function formaction($formaction)
    {
        $this->formaction = $formaction;
        return $this;
    }

    public function formenctype($formenctype)
    {
        $this->formenctype = $formenctype;
        return $this;
    }

    public function formmethod($formmethod)
    {
        $this->formmethod = $formmethod;
        return $this;
    }

    public function formnovalidate($formnovalidate)
    {
        $this->formnovalidate = $formnovalidate;
        return $this;
    }

    public function formtarget($formtarget)
    {
        $this->formtarget = $formtarget;
        return $this;
    }

    public function id($id)
    {
        $this->id = $id;
        return $this;
    }

    public function js($js)
    {
        $this->js = $js;
        return $this;
    }

    public function setClass($class)
    {
        $this->_class .= ' ' . $class;
        return $this;
    }

    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    public function onclick($onclick)
    {
        $this->onclick = $onclick;
        return $this;
    }

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    public function text($text)
    {
        $this->text = $text;
        return $this;
    }

    public function value($value)
    {
        $this->value = $value;
        return $this;
    }

    public function confirm($confirm)
    {
        $this->confirm = $confirm;
        return $this;
    }

    public function newtab($newtab)
    {
        $this->newtab = (bool) $newtab;
        return $this;
    }

    public function href($href)
    {
        $this->href = $href;
        return $this;
    }
}
