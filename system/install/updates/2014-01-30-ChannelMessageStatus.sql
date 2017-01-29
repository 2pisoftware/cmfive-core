CREATE TABLE IF NOT EXISTS `channel_message_status` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message_id` bigint(20) NOT NULL,
  `processor_id` bigint(20) NOT NULL,
  `message` text COLLATE utf8_unicode_ci,
  `is_successful` tinyint(1) NOT NULL DEFAULT '0',
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
