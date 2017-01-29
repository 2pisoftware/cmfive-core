

--
-- If you want to migrate to Timelog and REMOVE TaskTime
--

-- -- Update structure and rename
-- ALTER TABLE task_time
--     ADD COLUMN `object_table` VARCHAR(255) NOT NULL AFTER `id`,
--     CHANGE COLUMN `task_id` `object_id` BIGINT(20) NULL,
--     DROP COLUMN `comment_id`, 
--     ADD COLUMN `dt_modified` DATETIME NOT NULL AFTER `dt_created`, 
--     ADD COLUMN `modifier_id` BIGINT(20) NOT NULL AFTER `creator_id`, 
--     RENAME TO  `timelog`;
-- 
-- -- Change layout
-- ALTER TABLE timelog
--     CHANGE COLUMN `user_id` `user_id` BIGINT(20) NOT NULL AFTER `id`, 
--     CHANGE COLUMN `time_type` `time_type` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL AFTER `dt_end`, 
--     CHANGE COLUMN `dt_created` `dt_created` DATETIME NOT NULL AFTER `is_suspect`, 
--     CHANGE COLUMN `dt_modified` `dt_modified` DATETIME NOT NULL AFTER `dt_created`, 
--     CHANGE COLUMN `creator_id` `creator_id` BIGINT(20) NOT NULL AFTER `dt_modified`, 
--     CHANGE COLUMN `modifier_id` `modifier_id` BIGINT(20) NOT NULL AFTER `creator_id`, 
--     CHANGE COLUMN `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
--     CHANGE COLUMN `dt_end` `dt_end` DATETIME NULL;
-- 
-- -- Migrate data (All time logs were previously attached to Tasks)
-- UPDATE timelog SET object_class = "Task" WHERE 1;

--
-- If you want to migrate to Timelog and KEEP TaskTime
--

-- Table structure for table `timelog`
--

DROP TABLE IF EXISTS `timelog`;
CREATE TABLE `timelog` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `object_class` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_id` bigint(20) DEFAULT NULL,
  `dt_start` datetime NOT NULL,
  `dt_end` datetime NULL,
  `time_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_suspect` tinyint(4) NOT NULL DEFAULT '0',
  `dt_created` datetime NOT NULL,
  `dt_modified` datetime NOT NULL,
  `creator_id` bigint(20) NOT NULL,
  `modifier_id` bigint(20) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
