--
-- Database: `cmfive`
--
-- CREATE DATABASE IF NOT EXISTS `cmfive` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
-- USE `cmfive`;

-- --------------------------------------------------------

--
-- Table structure for table `attachment`
--

-- CREATE TABLE IF NOT EXISTS `attachment` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `parent_table` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `parent_id` bigint(20) NOT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_modified` datetime DEFAULT NULL,
--   `modifier_user_id` bigint(20) DEFAULT NULL,
--   `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `mimetype` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `description` text COLLATE utf8_unicode_ci,
--   `fullpath` text COLLATE utf8_unicode_ci NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `type_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `attachment_type`
--

-- CREATE TABLE IF NOT EXISTS `attachment_type` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `table_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `is_active` tinyint(1) NOT NULL DEFAULT '1',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `audit`
--

-- CREATE TABLE IF NOT EXISTS `audit` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `dt_created` datetime DEFAULT NULL,
--   `creator_id` bigint(20) DEFAULT NULL,
--   `submodule` text COLLATE utf8_unicode_ci,
--   `message` text COLLATE utf8_unicode_ci,
--   `module` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
--   `action` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
--   `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
--   `db_class` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `db_action` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `db_id` bigint(20) DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=77 ;

-- --------------------------------------------------------

--
-- Table structure for table `channel`
--

-- CREATE TABLE IF NOT EXISTS `channel` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `is_active` tinyint(1) NOT NULL DEFAULT '1',
--   `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `notify_user_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `notify_user_id` bigint(20) DEFAULT NULL,
--   `creator_id` bigint(20) NOT NULL,
--   `modifier_id` bigint(20) NOT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_modified` datetime NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `do_processing` tinyint(1) NOT NULL DEFAULT '1',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `channel_email_option`
--

-- CREATE TABLE IF NOT EXISTS `channel_email_option` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `channel_id` bigint(20) NOT NULL,
--   `server` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
--   `s_username` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `s_password` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `port` int(11) DEFAULT NULL,
--   `use_auth` tinyint(4) NOT NULL DEFAULT '1',
--   `folder` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `protocol` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `to_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `from_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `subject_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `cc_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `body_filter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `post_read_action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `post_read_parameter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `creator_id` bigint(20) NOT NULL,
--   `modifier_id` bigint(20) NOT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_modified` datetime NOT NULL,
--   `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `channel_message`
--

-- CREATE TABLE IF NOT EXISTS `channel_message` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `channel_id` bigint(20) NOT NULL,
--   `message_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `is_processed` tinyint(1) NOT NULL DEFAULT '0',
--   `creator_id` bigint(20) DEFAULT NULL,
--   `modifier_id` bigint(20) DEFAULT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_modified` datetime NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `channel_message_status`
--

-- CREATE TABLE IF NOT EXISTS `channel_message_status` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `message_id` bigint(20) NOT NULL,
--   `processor_id` bigint(20) NOT NULL,
--   `message` text COLLATE utf8_unicode_ci,
--   `is_successful` tinyint(1) NOT NULL DEFAULT '0',
--   `creator_id` bigint(20) DEFAULT NULL,
--   `modifier_id` bigint(20) DEFAULT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_modified` datetime NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `channel_processor`
--

-- CREATE TABLE IF NOT EXISTS `channel_processor` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `channel_id` bigint(20) NOT NULL,
--   `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `processor_settings` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `settings` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `creator_id` bigint(20) NOT NULL,
--   `modifier_id` bigint(20) NOT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_modified` datetime NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

-- CREATE TABLE IF NOT EXISTS `comment` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `obj_table` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
--   `obj_id` bigint(20) DEFAULT NULL,
--   `comment` text COLLATE utf8_unicode_ci,
--   `is_internal` tinyint(4) NOT NULL DEFAULT '0',
--   `is_system` tinyint(4) NOT NULL DEFAULT '0',
--   `creator_id` bigint(20) DEFAULT NULL,
--   `dt_created` datetime DEFAULT NULL,
--   `modifier_id` bigint(20) DEFAULT NULL,
--   `dt_modified` datetime DEFAULT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

-- CREATE TABLE IF NOT EXISTS `contact` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `firstname` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
--   `lastname` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `othername` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `title` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `homephone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `workphone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `mobile` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `priv_mobile` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `fax` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `email` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `notes` text COLLATE utf8_unicode_ci,
--   `dt_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `dt_modified` datetime NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `private_to_user_id` bigint(20) DEFAULT NULL,
--   `creator_id` bigint(20) DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_user`
--

-- CREATE TABLE IF NOT EXISTS `group_user` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `group_id` int(11) NOT NULL,
--   `user_id` int(11) NOT NULL,
--   `role` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
--   `is_active` tinyint(1) NOT NULL,
--   `dt_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `inbox`
--

-- CREATE TABLE IF NOT EXISTS `inbox` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `user_id` bigint(20) NOT NULL,
--   `sender_id` bigint(20) DEFAULT NULL,
--   `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `message_id` bigint(20) DEFAULT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_read` datetime DEFAULT NULL,
--   `is_new` tinyint(1) NOT NULL DEFAULT '1',
--   `dt_archived` datetime DEFAULT NULL,
--   `is_archived` tinyint(1) NOT NULL DEFAULT '0',
--   `parent_message_id` int(11) DEFAULT NULL,
--   `has_parent` tinyint(1) NOT NULL DEFAULT '0',
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `del_forever` tinyint(4) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `inbox_message`
--

-- CREATE TABLE IF NOT EXISTS `inbox_message` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `message` text COLLATE utf8_unicode_ci NOT NULL,
--   `digest` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `lookup`
--

-- CREATE TABLE IF NOT EXISTS `lookup` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `weight` int(11) DEFAULT NULL,
--   `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `object_history`
--

-- CREATE TABLE IF NOT EXISTS `object_history` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `class_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `object_id` bigint(20) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `object_history_entry`
--

-- CREATE TABLE IF NOT EXISTS `object_history_entry` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `history_id` bigint(20) NOT NULL,
--   `attr_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `attr_value` longtext COLLATE utf8_unicode_ci,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `object_index`
--

-- CREATE TABLE IF NOT EXISTS `object_index` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `dt_created` datetime DEFAULT NULL,
--   `dt_modified` datetime DEFAULT NULL,
--   `creator_id` bigint(20) DEFAULT NULL,
--   `modifier_id` bigint(20) DEFAULT NULL,
--   `class_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `object_id` bigint(20) NOT NULL,
--   `content` longtext COLLATE utf8_unicode_ci NOT NULL,
--   PRIMARY KEY (`id`),
--   FULLTEXT KEY `object_index_content` (`content`)
-- ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `object_modification`
--

-- CREATE TABLE IF NOT EXISTS `object_modification` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `table_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `object_id` bigint(20) NOT NULL,
--   `dt_created` datetime DEFAULT NULL,
--   `dt_modified` datetime DEFAULT NULL,
--   `creator_id` bigint(20) DEFAULT NULL,
--   `modifier_id` bigint(20) DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `printer`
--

-- CREATE TABLE IF NOT EXISTS `printer` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `name` varchar(512) NOT NULL,
--   `server` varchar(512) NOT NULL,
--   `port` varchar(256) DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

-- CREATE TABLE IF NOT EXISTS `report` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `report_connection_id` bigint(20) DEFAULT NULL,
--   `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `report_code` text COLLATE utf8_unicode_ci NOT NULL,
--   `is_approved` tinyint(1) NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `description` text COLLATE utf8_unicode_ci,
--   `sqltype` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_connection`
--

-- CREATE TABLE IF NOT EXISTS `report_connection` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `db_driver` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `db_host` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `db_port` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `db_database` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `db_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `s_db_user` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `s_db_password` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `creator_id` bigint(20) DEFAULT NULL,
--   `modifier_id` bigint(20) DEFAULT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_modified` datetime NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_feed`
--

-- CREATE TABLE IF NOT EXISTS `report_feed` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `report_id` int(11) NOT NULL,
--   `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `description` text COLLATE utf8_unicode_ci NOT NULL,
--   `report_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `url` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
--   `dt_created` datetime NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_member`
--
-- 
-- CREATE TABLE IF NOT EXISTS `report_member` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `report_id` int(11) NOT NULL,
--   `user_id` int(11) NOT NULL,
--   `role` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_template`
--

-- CREATE TABLE IF NOT EXISTS `report_template` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `report_id` bigint(20) NOT NULL,
--   `template_id` bigint(20) NOT NULL,
--   `type` varchar(255) DEFAULT NULL,
--   `creator_id` bigint(20) DEFAULT NULL,
--   `modifier_id` bigint(20) DEFAULT NULL,
--   `dt_created` datetime DEFAULT NULL,
--   `dt_updated` datetime DEFAULT NULL,
--   `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rest_session`
--

-- CREATE TABLE IF NOT EXISTS `rest_session` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `token` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
--   `user_id` bigint(20) NOT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_modified` datetime NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

-- CREATE TABLE IF NOT EXISTS `sessions` (
--   `session_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
--   `session_data` text COLLATE utf8_unicode_ci NOT NULL,
--   `expires` int(11) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`session_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--
-- 
-- CREATE TABLE IF NOT EXISTS `task` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `is_closed` tinyint(1) NOT NULL DEFAULT '0',
--   `parent_id` int(11) DEFAULT NULL,
--   `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `task_group_id` int(11) NOT NULL,
--   `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `priority` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `task_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `assignee_id` int(11) NOT NULL,
--   `dt_assigned` datetime NOT NULL,
--   `dt_first_assigned` datetime NOT NULL,
--   `first_assignee_id` int(11) NOT NULL,
--   `dt_completed` datetime DEFAULT NULL,
--   `dt_planned` datetime DEFAULT NULL,
--   `dt_due` datetime DEFAULT NULL,
--   `estimate_hours` int(11) DEFAULT NULL,
--   `description` text COLLATE utf8_unicode_ci,
--   `latitude` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `longitude` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
-- 
-- -- --------------------------------------------------------
-- 
-- --
-- -- Table structure for table `task_data`
-- --
-- 
-- CREATE TABLE IF NOT EXISTS `task_data` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `task_id` int(11) NOT NULL,
--   `data_key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
--   `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
-- 
-- -- --------------------------------------------------------
-- 
-- --
-- -- Table structure for table `task_group`
-- --
-- 
-- CREATE TABLE IF NOT EXISTS `task_group` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
--   `can_assign` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
--   `can_view` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
--   `can_create` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `is_active` tinyint(4) NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `description` text COLLATE utf8_unicode_ci NOT NULL,
--   `task_group_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
--   `default_assignee_id` int(11) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_group_member`
--

-- CREATE TABLE IF NOT EXISTS `task_group_member` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `task_group_id` int(50) NOT NULL,
--   `user_id` int(11) NOT NULL,
--   `role` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
--   `priority` int(11) NOT NULL,
--   `is_active` tinyint(4) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_group_notify`
--

-- CREATE TABLE IF NOT EXISTS `task_group_notify` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `task_group_id` int(11) NOT NULL,
--   `role` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `value` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_group_user_notify`
--

-- CREATE TABLE IF NOT EXISTS `task_group_user_notify` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `user_id` int(11) NOT NULL,
--   `task_group_id` int(11) NOT NULL,
--   `role` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `value` tinyint(1) DEFAULT '0',
--   `task_creation` tinyint(1) NOT NULL DEFAULT '0',
--   `task_details` tinyint(1) NOT NULL DEFAULT '0',
--   `task_comments` tinyint(1) NOT NULL DEFAULT '0',
--   `time_log` tinyint(1) NOT NULL DEFAULT '0',
--   `task_documents` tinyint(1) NOT NULL DEFAULT '0',
--   `task_pages` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_object`
--

-- CREATE TABLE IF NOT EXISTS `task_object` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `task_id` int(11) NOT NULL,
--   `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `table_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `object_id` int(11) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_time`
--

-- CREATE TABLE IF NOT EXISTS `task_time` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `task_id` int(11) NOT NULL,
--   `creator_id` int(11) NOT NULL,
--   `dt_created` datetime NOT NULL,
--   `user_id` int(11) NOT NULL,
--   `dt_start` datetime NOT NULL,
--   `dt_end` datetime NOT NULL,
--   `comment_id` int(11) NOT NULL,
--   `is_suspect` tinyint(4) NOT NULL DEFAULT '0',
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `time_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_user_notify`
--
-- 
-- CREATE TABLE IF NOT EXISTS `task_user_notify` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `user_id` int(11) NOT NULL,
--   `task_id` int(11) NOT NULL,
--   `task_creation` tinyint(1) NOT NULL DEFAULT '0',
--   `task_details` tinyint(1) NOT NULL DEFAULT '0',
--   `task_comments` tinyint(1) NOT NULL DEFAULT '0',
--   `time_log` tinyint(1) NOT NULL DEFAULT '0',
--   `task_documents` tinyint(1) NOT NULL DEFAULT '0',
--   `task_pages` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `template`
--

-- CREATE TABLE IF NOT EXISTS `template` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `template_title` text COLLATE utf8_unicode_ci,
--   `template_body` longtext COLLATE utf8_unicode_ci,
--   `test_title_json` text COLLATE utf8_unicode_ci,
--   `test_body_json` text COLLATE utf8_unicode_ci,
--   `is_active` tinyint(1) NOT NULL DEFAULT '0',
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `dt_created` datetime DEFAULT NULL,
--   `dt_modified` datetime DEFAULT NULL,
--   `creator_id` bigint(20) DEFAULT NULL,
--   `modifier_id` bigint(20) DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
-- 
-- CREATE TABLE IF NOT EXISTS `user` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `login` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
--   `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `password_salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `contact_id` bigint(20) DEFAULT NULL,
--   `password_reset_token` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
--   `dt_password_reset_at` timestamp NULL DEFAULT NULL,
--   `redirect_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'main/index',
--   `is_admin` tinyint(1) NOT NULL DEFAULT '0',
--   `is_active` tinyint(1) NOT NULL DEFAULT '1',
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `is_group` tinyint(4) NOT NULL DEFAULT '0',
--   `dt_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `dt_lastlogin` datetime DEFAULT NULL,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `login` (`login`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

-- CREATE TABLE IF NOT EXISTS `user_role` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `user_id` int(11) NOT NULL,
--   `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `unique_role_per_user` (`user_id`,`role`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `widget_config`
--

-- CREATE TABLE IF NOT EXISTS `widget_config` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `user_id` bigint(20) NOT NULL,
--   `destination_module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `source_module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `widget_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `creator_id` bigint(20) NOT NULL,
--   `custom_config` text COLLATE utf8_unicode_ci,
--   `modifier_id` bigint(20) NOT NULL,
--   `dt_created` datetime NOT NULL,
--   `dt_modified` datetime NOT NULL,
--   `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `tag`
--

-- CREATE TABLE IF NOT EXISTS `tag` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `obj_class` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
--   `obj_id` bigint(20) DEFAULT NULL,
--   `user_id` bigint(20) DEFAULT NULL,
--   `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `tag_color` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
--   `creator_id` bigint(20) DEFAULT NULL,
--   `dt_created` datetime DEFAULT NULL,
--   `modifier_id` bigint(20) DEFAULT NULL,
--   `dt_modified` datetime DEFAULT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   PRIMARY KEY (`id`),
--   INDEX deleted_tag_id(`is_deleted`, `tag`, `obj_class`, `obj_id`, `user_id`)
-- ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `favorite`
--
-- 
-- CREATE TABLE IF NOT EXISTS `favorite` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `object_class` varchar(255) NOT NULL,
--   `object_id` bigint(20) NOT NULL,
--   `user_id` bigint(20) NOT NULL,
--   `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
--   `dt_created` datetime NOT NULL,
--   `creator_id` bigint(20) NOT NULL,
--   `dt_modified` datetime NOT NULL,
--   `modifier_id` bigint(20) NOT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Table structure for table `migration`
--

-- CREATE TABLE IF NOT EXISTS `migration` (
--   `id` bigint(20) NOT NULL AUTO_INCREMENT,
--   `path` varchar(1024) NOT NULL,
--   `classname` varchar(1024) NOT NULL,
--   `module` varchar(1024) NOT NULL,
--   `dt_created` datetime DEFAULT NULL,
--   `creator_id` bigint(20) DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
