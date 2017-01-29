
CREATE TABLE `widget_config` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `destination_module` varchar(255) NOT NULL,
  `source_module` varchar(255) NOT NULL,
  `widget_name` varchar(255) NOT NULL,
  `creator_id` bigint(20) NOT NULL,
  `custom_config` TEXT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
