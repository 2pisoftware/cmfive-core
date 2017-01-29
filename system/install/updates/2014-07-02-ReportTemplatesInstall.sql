CREATE TABLE IF NOT EXISTS `report_template` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `report_id` bigint(20) NOT NULL,
  `template_id` bigint(20) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `dt_updated` datetime DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

