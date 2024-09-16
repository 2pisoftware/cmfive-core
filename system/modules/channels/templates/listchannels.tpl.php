<?php
echo HtmlBootstrap5::box("/channels-web/edit", "Add Web Channel", true, false, null, null, "isbox", null, "btn-sm btn-primary");
echo HtmlBootstrap5::box("/channels-email/edit", "Add Email Channel", true, false, null, null, "isbox", null, "btn-sm btn-primary");

if (!empty($channels)) {
    $table = array(array("ID", "Type", "Name", "Active", "Actions", "sort_key" => null));

    foreach ($channels as $c) {
        $base_channel = $c->getChannel();
        if (!empty($base_channel->id)) {
            $line = [];
            $line[] = $base_channel->id;
            $line[] = $c->_channeltype;
            $line[] = $base_channel->name;
            $line[] = $base_channel->is_active ? "Yes" : "No";
            $line[] = HtmlBootstrap5::buttonGroup(
                HtmlBootstrap5::b("/channels-{$c->_channeltype}/edit/{$base_channel->id}", "Edit", null, null, false, "btn btn-secondary") .
                    HtmlBootstrap5::dropdownButton(
                        "More",
                        [
                            HtmlBootstrap5::b("/channels/listmessages/{$base_channel->id}", "Messages", null, null, false, "dropdown-item btn-sm text-start"),
                            ($c->_channeltype == 'email' ? HtmlBootstrap5::b("/channels-email/test/{$base_channel->id}", 'Test Connection', null, null, false, "dropdown-item btn-sm text-start") : ''),
                            '<hr class="dropdown-divider">',
                            HtmlBootstrap5::b("/channels-{$c->_channeltype}/delete/{$base_channel->id}", "Delete", "Are you sure you want to delete " . (!empty($base_channel->name) ? 'the ' . addslashes($base_channel->name) . ' channel' : "this channel") . "?", null, false, "dropdown-item btn-sm text-start text-danger")
                        ],
                        "btn-info btn btn-sm rounded-0 rounded-end-1"
                    )
            );

            $line["sort_key"] = $base_channel->id;

            $table[] = $line;
        }
    }

    array_multisort(
        array_column($table, "sort_key"),
        SORT_ASC,
        $table
    );

    for ($i = 0, $length = count($table); $i < $length; ++$i) {
        array_pop($table[$i]);
    }

    echo HtmlBootstrap5::table($table, null, "tablesorter");
} else {
    echo "<p class='pt-3 text-center'>No Channels to list.</p>";
}
