<?php
class AdminMigration extends DbMigration {
	
	// =========== 0.8.0 ===============
	
	function upto_0_8_0() {
		
	}
	
	function downfrom_0_8_0() {
		
	}
		
	// =========== 0.7.0 ===============
	
	function upto_0_7_0() {
		// create audit table
		
		// create comment table
		
		// create printer table
		$printer = $this->createTable("printer");
		$printer->addField("name",$this->typeVarchar(255));
		$printer->addField("port",$this->typeVarchar(4));
		$printer->addField("server",$this->typeVarchar(255));
		$printer->execute();
		
		// create template table
		$template = $this->createTable("template");
		// id field is implicit!
		$template->addField("title",$this->typeVarchar(255),true);// name,type,NOT NULL,default
		$template->addField("module",$this->typeVarchar(255));
		$template->addField("category",$this->typeVarchar(255));
		$template->addField("template_title",$this->typeVarchar(1024));
		$template->addField("template_body",$this->typeLongText());
		$template->addField("description",$this->typeText());
		$template->addField("test_title_json",$this->typeVarchar(1024));
		$template->addField("test_title_body",$this->typeLongText());
		$template->addField("creator_id",$this->typeId()); // same as typeBigInt()
		$template->addField("modifier_id",$this->typeId()); // same as typeBigInt()
		$template->addField("modifier_id",$this->typeId()); // same as typeBigInt()
		$template->addField("dt_created",$this->typeDateTime()); 
		$template->addField("dt_modified",$this->typeDateTime()); 
		$template->addField("is_active",$this->typeBoolean()); 
		$template->addField("is_deleted",$this->typeBoolean()); 
		$template->execute();
		
		// create lookup table
		$lookup = $this->createTable("lookup");
		// id field is implicit!
		$lookup->addField("code",$this->typeVarchar(255));
		$lookup->addField("title",$this->typeVarchar(255));
		$lookup->addField("type",$this->typeVarchar(255));
		$lookup->addField("weight",$this->typeTinyInt());
		$lookup->addField("is_deleted",$this->typeBoolean());
		$lookup->execute();
		
	}
	
	function downfrom_0_7_0() {
		$this->dropTable("lookup")->execute();
		$this->dropTable("template")->execute();
		$this->dropTable("printer")->execute();
		$this->dropTable("comment")->execute();
		$this->dropTable("audit")->execute();
	}
}