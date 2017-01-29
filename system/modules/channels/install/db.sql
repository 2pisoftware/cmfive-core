CREATE TABLE IF NOT EXISTS `channel` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  `notify_user_email` varchar(255) DEFAULT NULL,
  `notify_user_id` bigint(20) DEFAULT NULL,
  `do_processing` tinyint(1) NOT NULL DEFAULT '1',
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;


CREATE TABLE IF NOT EXISTS `channel_email_option` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(20) NOT NULL,
  `server` varchar(1024) NOT NULL,
  `s_username` varchar(512) DEFAULT NULL,
  `s_password` varchar(512) DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `use_auth` tinyint(4) NOT NULL DEFAULT '1',
  `folder` varchar(256) DEFAULT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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


CREATE TABLE IF NOT EXISTS `channel_processor` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `channel_id` bigint(20) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `settings` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;


CREATE TABLE IF NOT EXISTS `channel_message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `channel_id` bigint(20) NOT NULL,
  `channel_type` varchar(255) NOT NULL,
  `is_processed` tinyint(1) NOT NULL DEFAULT '0',
  `creator_id` bigint(20) DEFAULT NULL,
  `modifier_id` bigint(20) DEFAULT NULL,
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
