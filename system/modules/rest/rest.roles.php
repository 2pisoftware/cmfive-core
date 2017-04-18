<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function role_rest_user_allowed(Web $w,$path) {
    return $w->checkUrl($path, "rest", null, "*");
}