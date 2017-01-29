ALTER TABLE `task` ADD `creator_id` BIGINT NULL AFTER `longitude`, 
                   ADD `modifier_id` BIGINT NULL AFTER `creator_id`, 
                   ADD `dt_created` DATETIME NULL AFTER `modifier_id`, 
                   ADD `dt_modified` DATETIME NULL AFTER `dt_created`;