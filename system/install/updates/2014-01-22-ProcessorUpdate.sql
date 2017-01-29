ALTER TABLE  `channel_processor` ADD  `name` VARCHAR( 255 ) NULL AFTER  `channel_id` ,
ADD  `processor_settings` VARCHAR( 1024 ) NULL AFTER  `name`;