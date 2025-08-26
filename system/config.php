<?php

//========= Anonymous Access ================================

// bypass authentication if sent from the following IP addresses
Config::set("system.allow_from_ip", []);

// or bypass authentication for the following modules
Config::set("system.allow_module", [
    // "rest", // uncomment this to switch on REST access to the database objects. Tread with CAUTION!
]);

Config::set('system.allow_action', [
    "auth/login",
    "auth/forgotpassword",
    "auth/resetpassword",
    "admin/datamigration",
    "install-steps/details",
    "install-steps/database",
    "install-steps/import",
    "install-steps/finish"
]);

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
 * Config::set('system.encryptionMigration', 'AdminSecurityAesToOpenssl');
 * OR simply by key, for systems > PHP8 where no forward path from AES can be migrated!
 */


// Adds ability to disable help and search
Config::set('system.help_enabled', true);
Config::set('system.search_enabled', true);

/**
 * Syntax for csrf config
 */
Config::set('system.csrf', [
    'enabled' => true,
    'protected' => [
        'auth' => [
            'login',
            'forgotpassword'
        ]
    ]
]);

Config::set('email.transports', [
    'ses' => 'SesTransport',
    'smtp' => 'SwiftMailerTransport',
    'swiftmailer' => 'SwiftMailerTransport',
    'sendmail' => 'SwiftMailerTransport',
    'aws' => 'AwsTransport',
    'mock' => 'MockTransport',
]);

Config::set('system.gc_maxlifetime', 21600);

Config::set('system.environment', ENVIRONMENT_PRODUCTION);

// For SendGrid API integration (also used for Mandrill integration)
// Config::append('email.api.credentials.key', '<your key>');


//======== Pass through authentication ===========
// Passtrough authentication currently only configured to work with LDAP and IIS
Config::set('system.use_passthrough_authentication', false);

Config::set("system.ldap", [
    'host'          => '192.168.0.256', // Host name or IP of LDAP server
    'port'          => 389, // 389 is default
    'username'      => 'DOMAIN\\User',
    'password'      => 'password',
    'domain'        => 'domain.example.com',
    'base_dn'       => 'DC=domain,DC=EXAMPLE,DC=COM',
    'auth_ou'       => 'OU=Users',
    'auth_search'   => '(cn={$username})', // {username} will be replaced in auth
    'search_filter_attribute' => [], // Here you can specify only certain attributes to get from ldap such as "ou" or "cn" etc
]);

Config::set('system.aws', [
    // Only used when system.environment is set to 'development'.
    'credentials' => [
        'key' => '',
        'secret' => '',
    ],
]);

Config::set('system.include_frame_options_header', true); // set to false to disable X-Frame-Options header

Config::set('system.use_api', true);
