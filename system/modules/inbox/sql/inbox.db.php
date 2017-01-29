<?php
function inbox_db_create() {
    $create['28/06/2010 08:00'] = "
    CREATE TABLE IF NOT EXISTS `inbox` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `user_id` bigint(20) NOT NULL,
        `sender_id` bigint(20) DEFAULT NULL,
        `subject` varchar(255) NOT NULL,
        `message_id` bigint(20) NOT NULL,
        `dt_created` datetime NOT NULL,
        `dt_read` datetime DEFAULT NULL,
        `is_new` tinyint(1) NOT NULL DEFAULT '1',
        `dt_archived` datetime DEFAULT NULL,
        `is_archived` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

    CREATE TABLE IF NOT EXISTS `inbox_message` (
        `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `message` TEXT NOT NULL ,
        `digest` VARCHAR( 255 ) NOT NULL
    ) ENGINE = MYISAM ;
";
}

?>
