ALTER TABLE `task` ADD `effort` FLOAT NULL DEFAULT NULL AFTER `priority`;
ALTER TABLE `task` CHANGE `estimate_hours` `estimate_hours` FLOAT(11) NULL DEFAULT NULL;
