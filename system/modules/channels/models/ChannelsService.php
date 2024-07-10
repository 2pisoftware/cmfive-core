<?php

// Theres a naming discrepancy where the service name didnt exactly match the module name
// While it doesnt really matter for general use, the navigation expects the names to match
class ChannelsService extends DbService
{
    /**
     * Channels naivgation function
     * @return none
     */
    public function navigation(Web $w, $title = null, $prenav = null)
    {
        if ($title) {
            $w->ctx("title", $title);
        }
        $nav = $prenav ? $prenav : [];
        if (AuthService::getInstance($w)->loggedIn()) {
            $w->menuLink("channels/listchannels", "List Channels", $nav);
            $w->menuLink("channels/listprocessors", "List Processors", $nav);
            $w->menuLink("channels/listmessages", "List Messages", $nav);
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }

    public function navList(): array
    {
        return [
            new MenuLinkStruct("List Channels", "channels/listchannels"),
            new MenuLinkStruct("List Processors", "channels/listprocessors"),
            new MenuLinkStruct("List Messages", "channels/listmessages"),
        ];
    }
}
