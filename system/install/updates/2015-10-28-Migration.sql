CREATE TABLE IF NOT EXISTS `migration` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `path` varchar(1024) NOT NULL,
  `classname` varchar(1024) NOT NULL,
  `dt_created` datetime DEFAULT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

