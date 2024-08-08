<?php

function ajax_confirm_mfa_code_POST(Web $w)
{
    $w->setLayout(null);

    $w->out((new JsonResponse())->setSuccessfulResponse("Auth enabled", null));
}
