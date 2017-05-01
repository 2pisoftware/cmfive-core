<?php namespace Html;

class input {
    
    public $title;
    public $type;
    public $name;
    public $value;
    public $size;
    public $extrafields;
    public $validation = array();
    public $readonly = false;
    
    public $id;
    public $_class;
    
    public $typeList = array("text" => "text", 
                             "password" => "password", 
                             "date" => "text", 
                             "datetime" => "text", 
                             "time" => "text",  
                             "checkbox" => "checkbox", 
                             "radio" => "radio", 
                             "hidden" => "hidden",
                             "file" => "file");
    
    public function __construct($data, $validation = array()) {
        if (count($data) >= 4) {
            $this->title = $data[0];
            $this->type = $data[1];
            $this->name = $data[2];
            $this->value = $data[3];
            $this->size = $data[4];
            $this->validation = $validation;
            
            // Check for readonly
            if ($name[0] == '-') {
                $name = substr($name, 1);
                $this->readonly = true;
            }
            
            if (count($data) > 5) {
                $extraFields = array_slice($data, 5);
            }
        }
    }

    public function __toString() {
        // Check if type is correct
        if (!in_array(strtolower($this->type), $this->typeList)) return;
        
        // Open tag and data
        $buffer = "<input type='{$this->typeList[$this->type]}' name='{$this-name}' ";
        if (!empty($this->id)) {
            $buffer .= "id='{$this->id}' ";
        }
        if (!empty($this->value)) {
            $buffer .= "value='{$this->value}' ";
        }
        
        
        
        switch($type) {
            case "text":
            case "password":
                if ($this->readonly) {
                    $buffer .= " readonly='true' ";
                }
            break;
            case "date":
                $this->_class .= " date_picker";
            
            break;
        }
        
        if (!empty($this->_class)) {
            $buffer .= "class='{$this->_class}' ";
        }
        
        // Close tag
        $buffer .= " />";
    }
    
}
    