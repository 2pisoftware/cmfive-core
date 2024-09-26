<?php

declare(strict_types=1);

use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

function ajax_register_authenticator_POST(Web $w)
{
    $w->setLayout(null);


    $user_id = Request::int("id");
    if (empty($user_id)) {
        $w->out((new JsonResponse())->setErrorResponse("Request data missing", null));
        return;
    }

    // RP Entity i.e. the application
    $rpEntity = PublicKeyCredentialRpEntity::create(
        'CMfive', //Name
        'cmfive.com',              //ID
        null                            //Icon
    );

    // User Entity
    $userEntity = PublicKeyCredentialUserEntity::create(
        'admin',                   //Name
        $user_id, //ID
        'admin admin',                          //Display name
        null                                    //Icon
    );

    // Challenge
    $challenge = random_bytes(16);

    $publicKeyCredentialCreationOptions =
        PublicKeyCredentialCreationOptions::create(
            $rpEntity,
            $userEntity,
            $challenge
        )
    ;


    $w->out((new JsonResponse())->setSuccessfulResponse("Auth enabled", null));
}
