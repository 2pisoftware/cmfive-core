ALTER TABLE `comment` CHANGE `is_deleted` `is_deleted` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `wiki` CHANGE `is_deleted` `is_deleted` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `task_group` CHANGE `is_deleted` `is_deleted` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `report_member` CHANGE `is_deleted` `is_deleted` tinyint(1) NOT NULL DEFAULT '0';

