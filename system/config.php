<?php

//========= Anonymous Access ================================

// bypass authentication if sent from the following IP addresses
Config::set("system.allow_from_ip", array());

// or bypass authentication for the following modules
Config::set("system.allow_module", array(
    // "rest", // uncomment this to switch on REST access to the database objects. Tread with CAUTION!
));

Config::set('system.allow_action', array(
    "auth/login",
    "auth/forgotpassword",
    "auth/resetpassword",
    "admin/datamigration",
    "install-steps/details",
    "install-steps/database",
    "install-steps/import",
	"install-steps/finish"
));

/**
 * The password salt is used by the AES encryption library
 * The salt length HAS to be 16, 24, or 32 characters long (8-bit)
 *
 * The easiest way to generate a 32 char salt is to use MD5
 */
Config::set('system.password_salt', md5('override this in your project config'));

/**
 * Otherwise, SSL will be used with KEY & IV from config,
 * if system has been upgraded per this migration:
 */

Config::set('system.encryptionMigration', 'AdminSecurityAesToOpenssl');


/**
 * Syntax for csrf config
 */
Config::set('system.csrf', array(
    'enabled' => true,
    'protected' => array(
        'auth' => array(
            'login',
            'forgotpassword'
        )
    )
));

Config::set('email.transports', array(
	'smtp' => 'SwiftMailerTransport',
	'swiftmailer' => 'SwiftMailerTransport',
	'sendmail' => 'SwiftMailerTransport'
));

Config::set('system.gc_maxlifetime', 21600);

Config::set('system.environment', 'development');

// For SendGrid API integration (also used for Mandrill integration)
// Config::append('email.api.credentials.key', '<your key>');


//======== Pass through authentication ===========
// Passtrough authentication currently only configured to work with LDAP and IIS
Config::set('system.use_passthrough_authentication', false);

Config::set("system.ldap", array(
		'host'          => '192.168.0.256', // Host name or IP of LDAP server
		'port'          => 389, // 389 is default
		'username'      => 'DOMAIN\\User',
		'password'      => 'password',
		'domain'        => 'domain.example.com',
		'base_dn'       => 'DC=domain,DC=EXAMPLE,DC=COM',
		'auth_ou'       => 'OU=Users',
		'auth_search'   => '(cn={$username})', // {username} will be replaced in auth
		'search_filter_attribute' => array(), // Here you can specify only certain attributes to get from ldap such as "ou" or "cn" etc
));
