--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `obj_class` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `obj_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tag_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `creator_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_modified` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX deleted_tag_id(`is_deleted`, `tag`, `obj_class`, `obj_id`, `user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------