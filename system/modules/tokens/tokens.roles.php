<?php


// function role_tokens_admin_token_allowed(Web $w,$path) {
//     return $w->checkUrl($path, "tokens", "*", "*");
// }

function role_tokens_grant_allowed(Web $w,$path) {
    return $w->checkUrl($path, "tokens", "", "grant");
}

function role_tokens_info_api_allowed(Web $w,$path) {
    return $w->checkUrl($path, "tokens", "api", "info");
}

function role_tokens_test_api_allowed(Web $w,$path) {
    return $w->checkUrl($path, "tokens", "api", "test");
}

function role_tokens_request_api_allowed(Web $w,$path) {
    return $w->checkUrl($path, "tokens", "api", "loopback");
}

