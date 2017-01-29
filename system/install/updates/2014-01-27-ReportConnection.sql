CREATE TABLE IF NOT EXISTS `report_connection` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `db_driver` varchar(255) NOT NULL,
  `db_host` varchar(255) NULL,
  `db_port` varchar(255) NULL,
  `db_database` varchar(255) NULL,
  `db_file` varchar(255) NULL,
  `s_db_user` varchar(255) NULL,
  `s_db_password` varchar(255) NULL,  
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE  `report` ADD  `report_connection_id` bigint(20) NULL AFTER  `id`;