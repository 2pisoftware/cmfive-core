<?php

function moveToAdapter_GET(Web $w)
{
    $from_adapter = Request::string('from_adapter');
    $to_adapter = Request::string('to_adapter');
    $max_count = Request::string('max_count');

    if (empty($max_count)) {
        $max_count = -1;
    }

    if (!empty(Config::get('file.adapters.' . $to_adapter)) && Config::get('file.adapters.' . $to_adapter . '.active') === true) {
        if (empty(Config::get('file.adapters.' . $from_adapter))) {
            $w->error('Origin adapter "' . $from_adapter . '" is not found', '/file-admin');
        }

        // From index
        $count = 0;
        $skipped = 0;
        $attachments = FileService::getInstance($w)->getAttachmentsForAdapter($from_adapter);
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                if ($max_count >= 0 && $count >= $max_count) {
                    break;
                }
                try {
                    $attachment->moveToAdapter($to_adapter);
                    $count++;
                } catch (Exception $e) {
                    LogService::getInstance($w)->error($e->getMessage());
                    $skipped++;
                }
            }
        }
        $message = ($count . ' attachment' . ($count == 1 ? '' : 's') . ' moved from "' . $from_adapter . '" to "' . $to_adapter . '"');
        if ($skipped > 0) {
            $message = $message . ('<br>' . $skipped . ' attachment' . ($skipped == 1 ? '' : 's') . ' skipped, check logs.');
        }
        $w->msg($message, '/file-admin');
    } else {
        $w->error('Target adapter "' . $to_adapter . '" is either not found or not active', '/file-admin');
    }
}
