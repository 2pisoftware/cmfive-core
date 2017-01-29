INSERT INTO `contact` (`id`, `firstname`, `lastname`, `othername`, `title`, `homephone`, `workphone`, `mobile`, `priv_mobile`, `fax`, `email`, `notes`, `dt_created`, `dt_modified`, `is_deleted`, `private_to_user_id`, `creator_id`) VALUES
(1, 'Administrator', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin@tripleacs.com', NULL, '2012-04-27 06:31:52', '0000-00-00 00:00:00', 0, NULL, NULL);

INSERT INTO `user` (`id`, `login`, `password`, `password_salt`, `contact_id`, `is_admin`, `is_active`, `is_deleted`, `is_group`, `dt_created`, `dt_lastlogin`) VALUES
(1, 'admin', 'ca1e51f19afbe6e0fb51dde5bcf01ab73e52c7cd', '9b618fbc7f9509fc28ebea98becfdd58', 1, 1, 1, 0, 0, '2012-04-27 06:31:07', '2012-04-27 17:23:54');

