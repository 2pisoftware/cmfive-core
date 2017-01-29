<?php

class MainService extends DbService {

    public function getUserRedirectURL() {
        // Redirect to users redirect_url
        $redirect_url = "main/index";
        if (!empty($this->w->Auth->user()->redirect_url)) {
            $redirect_url = $this->w->Auth->user()->redirect_url;
        }

        // Filter out everything except the path so that users cant make redirect urls out of cmfive
        $parse_url = parse_url($redirect_url);
        $url = $parse_url["path"];

        // Menu link doesnt like a lead slash
        if ($url[0] == "/") {
            $url = substr($url, 1);
        }
        return $url;
    }
    
}
