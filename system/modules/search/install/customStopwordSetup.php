<?php
//To be run by a super admin
	$sql = "SET GLOBAL innodb_ft_server_stopword_table = 'cmfive/search_stopwords';";
	$this->_db->sql ( $sql )->execute ();
	