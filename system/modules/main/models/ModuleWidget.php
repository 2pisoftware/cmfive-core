<?php

/**
 * This class serves as an abstract (pseudo interface) for any Widget.
 * When creating a Widget, extend this class for access to the DbService
 * and to ensure concurrency between widgets and their implementation
 */
abstract class ModuleWidget extends DbService {

	public $module_config = null;

	/**
	 *	Instantiate with Web so the module has access to models and output
	 *
	 *	@param Web $w;
	 */
	public function __construct(Web & $w, $module_config = null) {
		if (!empty($module_config)) {
			$this->module_config = $module_config;
		}
		parent::__construct($w);
	}

	/** 
	 * This settings array will be Widget specific and saved to the DB
	 * as a JSON string ensuring transportability The returned array 
	 * must match the Html::multiColForm() nested array structure so 
	 * that these settings can be changed
	 *
	 * @param String $current_settings
	 * @return Array
	 */
	public function getSettingsForm($current_settings = null) {
		return [];
	}

	/**
	 * This is where the widget prints out its content
	 *
	 * @return null
	 */
	public function display() {
		return null;
	}

	/**
	 * This function defines the roles that are required (at least one) to access
	 * the widget.
	 * 
	 * WARNING: If the widget doesn't define one it will be available to everyone!
	 * 
	 * @return Array
	 */
	public function canView(User $user) {
		return true;
	}
}
