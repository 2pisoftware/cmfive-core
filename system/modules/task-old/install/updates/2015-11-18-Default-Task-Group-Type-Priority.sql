-- The following changes were made to db.sql
ALTER TABLE `task_group` ADD `default_task_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE `task_group` ADD `default_priority` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL;
