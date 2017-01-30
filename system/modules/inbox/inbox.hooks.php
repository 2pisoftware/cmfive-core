<?php

function inbox_admin_account_changed(Web $w, $account = null) {
    $message = __("The following account has been changed").":\n" . @json_encode($account, JSON_PRETTY_PRINT);
    $w->Inbox->addMessage(__("An account has changed"), $message, $account->creator_id);
}
