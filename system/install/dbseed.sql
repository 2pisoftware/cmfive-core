/*INSERT INTO `contact` (`id`, `firstname`, `lastname`, `othername`, `title`, `homephone`, `workphone`, `mobile`, `priv_mobile`, `fax`, `email`, `notes`, `dt_created`, `dt_modified`, `is_deleted`, `private_to_user_id`, `creator_id`) VALUES
(1, 'Administrator', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin@tripleacs.com', NULL, '2012-04-27 06:31:52', '0000-00-00 00:00:00', 0, NULL, NULL);

INSERT INTO `user` (`id`, `login`, `password`, `password_salt`, `contact_id`, `is_admin`, `is_active`, `is_deleted`, `is_group`, `dt_created`, `dt_lastlogin`) VALUES
(1, 'admin', 'ca1e51f19afbe6e0fb51dde5bcf01ab73e52c7cd', '9b618fbc7f9509fc28ebea98becfdd58', 1, 1, 1, 0, 0, '2012-04-27 06:31:07', '2012-04-27 17:23:54');
*/

INSERT INTO `report` (`id`, `title`, `module`, `category`, `report_code`, `is_approved`, `is_deleted`, `description`, `sqltype`) VALUES
(1, 'Audit', 'admin', '', '[[dt_from||date||Date From]]\r\n\r\n[[dt_to||date||Date To]]\r\n\r\n[[user_id||select||User||select u.id as value, concat(c.firstname,'' '',c.lastname) as title from user u, contact c where u.contact_id = c.id order by title]]\r\n\r\n[[module||select||Module||select distinct module as value, module as title from audit order by module asc]]\r\n\r\n[[action||select||Action||select distinct action as value, concat(module,''/'',action) as title from audit order by title]]\r\n\r\n@@Audit Report||\r\n\r\nselect \r\na.dt_created as Date, \r\nconcat(c.firstname,'' '',c.lastname) as User,  \r\na.module as Module,\r\na.path as Url,\r\na.db_class as ''Class'',\r\na.db_action as ''Action'',\r\na.db_id as ''DB Id''\r\n\r\nfrom audit a\r\n\r\nleft join user u on u.id = a.creator_id\r\nleft join contact c on c.id = u.contact_id\r\n\r\nwhere \r\na.dt_created >= ''{{dt_from}} 00:00:00'' \r\nand a.dt_created <= ''{{dt_to}} 23:59:59'' \r\nand (''{{module}}'' = '''' or a.module = ''{{module}}'')\r\nand (''{{action}}'' = '''' or a.action = ''{{action}}'') \r\nand (''{{user_id}}'' = '''' or a.creator_id = ''{{user_id}}'')\r\n\r\n@@\r\n', 1, 0, 'Show Audit Information', 'select'),
(2, 'Contacts', 'admin', '', '@@Contacts||\r\nselect * from contact\r\n@@', 0, 0, '', 'select');


INSERT INTO `report_member` (`id`, `report_id`, `user_id`, `role`, `is_deleted`) VALUES
(1, 1, 1, 'OWNER', 0),
(2, 2, 1, 'OWNER', 0);


