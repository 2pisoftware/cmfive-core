<?php
if (!empty($statuses)) {
    $table = array(array("Processor ID", "Message", "Was Successful", "Actions", "sort_key" => null));

    foreach ($statuses as $s) {
        $message = ChannelService::getInstance($w)->getMessage($s->message_id);
        $line = [];
        $line[] = $s->processor_id;
        $line[] = $s->message;
        $line[] = $s->is_successful ? "Yes" : "No";
        $line[] = HtmlBootstrap5::a("/channels/process/{$message->channel_id}", "Rerun Process");
        $line["sort_key"] = $s->processor_id;

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
    echo "<p class='pt-3 text-center'>No Message Statuses to list.</p>";
}