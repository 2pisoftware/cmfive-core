<?php

class CmfiveScriptComponent extends CmfiveComponent {

    public $tag = 'script';
    public $has_closing_tag = true;

    public function __construct($path, Array $props = []) {
		$this->src = $path;

		if (!empty($props)) {
		    foreach($props as $key => $value) {
				$this->$key = $value;
			}
		}
    }

}
