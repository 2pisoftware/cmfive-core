<?php

class CmfiveScriptComponent extends CmfiveComponent {

	public $tag = 'script';
	public $has_closing_tag = true;

	public function __construct($path) {
		$this->src = $path;
	}

}