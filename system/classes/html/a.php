<?php

namespace Html;

/**
 * \Html\a class for generation and tight control of "a" links within cmfive
 * 
 * @author Adam Buckley <adam.buckley90@gmail.com>
 */
class a
{
    use GlobalAttributes;

    public $text;
    public $confirm;
    public $download; // New to HTML5
    public $href;
    public $hreflang;
    public $media; // New to HTML5
    public $rel;
    public $target;
    public $type; // New in HTML5

    public $onclick;

    public function __construct()
    {
    }

    public function __set($property, $value)
    {
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

    // A static list of labels to exclude from the output string
    public static $_excludeFromOutput = [
        "text", "onclick", "confirm"
    ];

    /**
     * Returns built string of input field
     * 
     * @return string string representation
     */
    public function __toString()
    {
        $buffer = '<a ';

        if (!empty($this->onclick)) {
            $buffer .= "onclick=\"{$this->onclick}\"";
        } else {
            if (!empty($this->confirm)) {
                $buffer .= "onclick=\"javascript:return confirm('" . $this->confirm . "');\" ";
            }
        }

        foreach (get_object_vars($this) as $field => $value) {
            if (!is_null($value) && !in_array($field, static::$_excludeFromOutput) && $field[0] !== "_") {
                $buffer .= $field . '=\'' . $this->{$field} . '\' ';
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

        return $buffer . '>' . $this->text . '</a>';
    }


    // public function __toString() {
    //     $buffer = "<a ";
    //     if (!empty($this->onclick)){
    //         $buffer .= "onclick=\"{$this->onclick}\"";
    //     } else {
    //         if (!empty($this->confirm)) {
    //             $buffer .= "onclick=\"javascript:return confirm('" . $this->confirm . "');\" ";
    //         }
    //     }
    //     if (!empty($this->download)) $buffer .= "download='{$this->download}' ";
    //     if (!empty($this->href)) $buffer .= "href='{$this->href}' ";
    //     if (!empty($this->hreflang)) $buffer .= "hreflang='{$this->hreflang}' ";
    //     if (!empty($this->media)) $buffer .= "media='{$this->media}' ";
    //     if (!empty($this->id)) $buffer .= "id='{$this->id}' ";
    //     if (!empty($this->class)) $buffer .= "class='{$this->class}' ";
    //     if (!empty($this->rel)) $buffer .= "rel='{$this->rel}' ";
    //     if (!empty($this->target)) $buffer .= "target='{$this->target}' ";
    //     if (!empty($this->type)) $buffer .= "type='{$this->type}' ";
    //     $buffer .= (">{$this->text}</a>");
    //     return $buffer;
    // }

    public function confirm($confirmation)
    {
        $this->confirm = $confirmation;
        return $this;
    }

    public function text($text)
    {
        $this->text = $text;
        return $this;
    }

    public function download($path)
    {
        $this->download = $path;
        return $this;
    }

    public function href($uri)
    {
        $this->href = $uri;
        return $this;
    }

    public function hreflang($lang)
    {
        $this->hreflang = $lang;
        return $this;
    }

    public function media($media)
    {
        $this->media = $media;
        return $this;
    }

    public function id($id)
    {
        $this->id = $id;
        return $this;
    }

    public function onclick($onclick)
    {
        $this->onclick = $onclick;
        return $this;
    }

    /**
     * Class is a reserved word so we have to use setClass
     * 
     * @param string $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    public function rel($rel)
    {
        $this->rel = $rel;
        return $this;
    }

    public function target($target)
    {
        $this->target = $target;
        return $this;
    }

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }
}
