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