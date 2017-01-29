--
-- Table structure for table `favorite`
--

CREATE TABLE IF NOT EXISTS `favorite` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_class` varchar(255) NOT NULL,
  `object_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `dt_created` datetime NOT NULL,
  `creator_id` bigint(20) NOT NULL,
  `dt_modified` datetime NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
