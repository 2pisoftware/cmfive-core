CREATE TABLE IF NOT EXISTS `channel_web_option` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(20) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

