<?php

function tokens_auth_get_auth_token_validation(Web $w, $jwt) {
    return TokensService::getInstance($w)->getCoreTokenCheck($jwt);
}
