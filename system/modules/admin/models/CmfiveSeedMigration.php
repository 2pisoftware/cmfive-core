<?php

abstract class CmfiveSeedMigration {

	protected $w;
	public function __construct(Web $w) {
		$this->w = $w;
	}

	public $name = '';
	public $description = '';

	public function seed() {
		
	}

}
