//======= Override Main Module Company Parameters ============

Config::set("main.application_name", "{{ application_name }}");
Config::set("main.company_name", "{{ company_name }}");
Config::set("main.company_url", "{{ company_url }}");

// enter a valid email address

Config::set("main.company_support_email","{{ company_support_email }}");

//=============== Timezone ===================================

Config::set("system.timezone", "{{ timezone }}");

//========== Database Configuration ==========================

Config::set("database", array(
    "hostname"   => "{{ db_host }}",
    "username"   => "{{ db_username }}",
    "password"   => "{{ db_password }}",
    "database"   => "{{ db_database }}",
    "driver"     => "{{ db_driver }}"
));

//=========== Email Layer Configuration =====================

Config::set("email", [
    "layer"      => "{{ email_transport }}",		// local, external smtp or sendmail
    "command"    => "{{ email_sendmail }}",		// used for sendmail layer only
    "host"       => "{{ email_host }}",
	"port"       => "{{ email_port }}",
    "encryption" => "{{ email_encryption }}", // none, SSL or TLS
	"auth"       => "{{ email_auth }}",
    "username"	 => "{{ email_username }}",
    "password"	 => "{{ email_password }}",
]);

//========= Anonymous Access ================================

// bypass authentication if sent from the following IP addresses
// specify an IP address and an array of allowed actions from that IP

Config::set("system.allow_from_ip", []);

// or bypass authentication for the following modules

Config::set("system.allow_module", []);

// or bypass authentication for the following actions

Config::set("system.allow_action", [
    "auth/login",
    "auth/forgotpassword",
    "auth/resetpassword",
    "install/ajax_checkconnection",
	"install/7/complete"
]);

//========= REST Configuration ==============================
// check the following configuration carefully to secure
// access to the REST infrastructure.
//===========================================================

// use the API_KEY to authenticate with username and password

Config::set("system.rest_api_key", "{{ rest_api_key }}");

// include class of objects that you want available via REST
// be aware that only the listed objects will be available via
// the REST API

Config::set("system.rest_include", []);

