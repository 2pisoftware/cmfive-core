<?php

// Theres a naming discrepancy where the service name didnt exactly match the module name
// While it doesnt really matter for general use, the navigation expects the names the match
class ChannelsService extends DbService {
    
    /**
     * Channels naivgation function
     * @return none
     */
    public function navigation(Web $w, $title = null, $prenav=null) {
        if ($title) {
            $w->ctx("title",$title);
        }
        $nav = $prenav ? $prenav : array();
        if ($w->Auth->loggedIn()) {
            $w->menuLink("channels/listchannels",__("List Channels"), $nav);
            $w->menuLink("channels/listprocessors",__("List Processors"), $nav);
            $w->menuLink("channels/listmessages",__("List Messages"), $nav);
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }
    
}
