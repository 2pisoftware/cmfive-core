<?php

abstract class CmfiveComponent {

	public $tag = 'link';
	public $has_closing_tag = false;

	public $is_included = false;

	public static $_excludeFromOutput = ['tag', 'has_closing_tag'];

	/**
	 * Returns the HTML to include this component on the page
	 * 
	 * @return string component include code
	 */
	public function _include() {
		if ($this->is_included) {
			return '';
		}

		$this->is_included = true;
		
		$buffer = '<' . $this->tag . ' ';

		foreach(get_object_vars($this) as $field => $value) {
			if (!is_null($value) && !in_array($field, static::$_excludeFromOutput)) {
				$buffer .= $field . '=\'' . $this->{$field} . '\' ';
			}
		}

		return $buffer . ($this->has_closing_tag ? '></' . $this->tag . '>' : '/>');
	}

	/**
	 * Should return the component itself, some components may not need this like link/script components
	 * 
	 * @return string component
	 */
	public function display($binding_data = []) {
		return '';
	}

}