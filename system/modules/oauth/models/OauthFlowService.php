<?php

/**@author Derek Crannaford */

class OauthFlowService extends DbService
{

    public function getOauthFlowByState($state)
    {
        return $this->getObject("OauthFlow", ['state' => $state]);
    }

    public function getOauthAppByProvider($provider = "", $app = null)
    {
        $ownedApps = Config::get('oauth.apps.' . $provider);

        return $ownedApps[$app] ?? null;
    }

    public function getOauthAppById($app)
    {
        $providers = Config::get('oauth.apps');
        foreach ($providers as $provider) {
            if (!empty($provider[$app])) {
                $pack = $provider[$app];
                $pack['provider'] = $provider;
                return $pack;
            }
        }
        return null;
    }

    
    public function getOauthSplashPageTemplate($title)
    { 
            $where['module'] = "oauth"; 
            $where['category'] = "splashpage"; 
            $where['is_active'] = 1; 
            $where['is_deleted'] = 0;
            $where['title'] = $title;
        return $this->getObject("Template", $where);
    }
}
