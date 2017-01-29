CREATE TABLE `printer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) NOT NULL,
  `server` varchar(512) NOT NULL,
  `port` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

