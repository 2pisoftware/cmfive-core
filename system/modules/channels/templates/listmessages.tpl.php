<?php
if (!empty($messages)) {
    $table = array(array("ID", "Type", "Channel", "Failed Processes", "Time Recieved", "Actions", "sort_key" => null));

    foreach ($messages as $m) {
        $channel = $m->getChannel();
        $line = [];
        $line[] = $m->id;
        $line[] = $m->message_type;
        $line[] = (!empty($channel->id) ? $channel->name : "");
        $line[] = $m->getFailedProcesses();
        $line[] = formatDateTime($m->dt_created);
        $line[] = HtmlBootstrap5::a("/channels/listmessagestatuses/{$m->id}", "View Message Statuses");

        $line["sort_key"] = $m->id;

        $table[] = $line;
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
    echo "<p>No messages found.</p>";
}