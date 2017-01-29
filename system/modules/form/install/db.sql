
CREATE TABLE IF NOT EXISTS `form` (
`id` bigint(20) AUTO_INCREMENT NOT NULL,
  `title` varchar(256) NOT NULL,
  `description` varchar(1024) DEFAULT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
   PRIMARY KEY(`id`)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `form_field` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`form_id` BIGINT NOT NULL,
	`name` VARCHAR(256) NOT NULL,
	`technical_name` VARCHAR(256) NOT NULL,
	`interface_class` VARCHAR(256) NULL,
	`type` VARCHAR(256) NOT NULL,
	`mask` VARCHAR(1024) NULL,
	`is_deleted` TINYINT(1) NOT NULL DEFAULT '0',
	`creator_id` BIGINT NOT NULL,
	`modifier_id` BIGINT NOT NULL,
	`dt_created` DATETIME NOT NULL,
	`dt_modified` DATETIME NOT NULL,
	PRIMARY KEY (`id`) 
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `form_value` ( 
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`form_instance_id` BIGINT NOT NULL,
	`form_field_id` BIGINT NOT NULL,
	`value` VARCHAR(1024) NULL,
	`field_type` VARCHAR(256) NOT NULL,
	`mask` VARCHAR(1024) NULL,
	`is_deleted` TINYINT(1) NOT NULL DEFAULT '0',
	`creator_id` BIGINT NOT NULL,
	`modifier_id` BIGINT NOT NULL,
	`dt_created` DATETIME NOT NULL,
	`dt_modified` DATETIME NOT NULL,
	PRIMARY KEY (`id`) 
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `form_field_metadata` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`form_field_id` BIGINT NOT NULL,
	`meta_key` VARCHAR(256) NOT NULL,
	`meta_value` VARCHAR(256) NULL,
	`is_deleted` TINYINT(1) NOT NULL DEFAULT '0',
	`creator_id` BIGINT NOT NULL,
	`modifier_id` BIGINT NOT NULL,
	`dt_created` DATETIME NOT NULL,
	`dt_modified` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `form_instance` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`form_id` BIGINT NOT NULL,
	`object_class` VARCHAR(256) NOT NULL,
	`object_id` BIGINT NOT NULL,
	`is_deleted` TINYINT(1) NOT NULL DEFAULT '0',
	`creator_id` BIGINT NOT NULL,
	`modifier_id` BIGINT NOT NULL,
	`dt_created` DATETIME NOT NULL,
	`dt_modified` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `form_mapping` ( 
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`form_id` BIGINT NOT NULL,
	`object` VARCHAR(256) NOT NULL,
	`is_deleted` TINYINT NOT NULL DEFAULT '0',
	`creator_id` BIGINT NOT NULL,
	`modifier_id` BIGINT NOT NULL,
	`dt_created` DATETIME NOT NULL,
	`dt_modified` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;
