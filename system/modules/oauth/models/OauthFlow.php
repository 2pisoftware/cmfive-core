<?php

/**@author Derek Crannaford */

class OauthFlow extends DbObject
{
    public $app_id;
    public $state;
    public $pkce_challenge;
    public $pkce_method;
    public $pkce_verifier;

    /*
    Some basic stuff about the code_verifier, code_challenge and the code_challenge_method:
        code_verifier — The code verifier should be a high-entropy cryptographic random string with a minimum of 43 characters and a maximum of 128 characters. Should only use A-Z, a-z, 0–9, “-”(hyphen), “.” (period), “_”(underscore), “~”(tilde) characters.
        code_challenge — The code challenge is created by SHA256 hashing the code_verifier and base64 URL encoding the resulting hash Base64UrlEncode(SHA256Hash(code_verifier)). In the case that there is no such possibility ever to do the above transformation it is okay to use the code_verifier itself as the code_challenge.
        code_challenge_method — This is an optional parameter. The available values are “S256” when using a transformed code_challenge as mentioned above. Or “plain” when the code_challenge is the same as the code_verifier. Since this is an optional parameter, if not sent in the request the Authorisation Server will assign “plain” as the default value.
        */

    // On advice for handling in PHP, from
    // https://devforum.okta.com/t/how-to-do-pkce-challenge-in-php/553
    public function __construct(Web $w)
    {
        parent::__construct($w);

        $tokenService = TokensService::getInstance($this->w);

        $random = bin2hex(openssl_random_pseudo_bytes(32));
        $this->pkce_verifier = $tokenService->getBase64URL(pack('H*', $random));
        $this->pkce_challenge = $tokenService->getBase64URL(pack('H*', hash('sha256', $this->pkce_verifier)));
        $this->pkce_method = "S256";

        $random = bin2hex(openssl_random_pseudo_bytes(32));
        $this->state = $tokenService->getBase64URL(pack('H*', $random));

        parent::insert();
    }

/*
 "hostname"  => getenv('DB_HOST') ? : "mysqldb",
    "port" => '3306',
    "username"  => getenv('DB_USERNAME') ? : "cm5",
    "password"  => getenv('DB_PASSWORD') ? : "cm5MySQL",
    "database"  => getenv('DB_DATABASE') ? : "cm5code",
    */
}
