<?php

/**@author Derek Crannaford */

class TokensService extends DbService
{

    public function getTokenFromAuthorisationHeader($bearer): string
    {
        if (!preg_match('/Bearer\s(\S+)/', $bearer, $matches)) {
            return "";
        }
        return $matches[1] ?? "";
    }

    public function getJsonFromPostRequest(): array
    {
        // Takes raw data from the request
        $json = file_get_contents('php://input');

        // Converts it into a PHP object
        return json_decode($json, true) ?? ['error' => "json parse failed"];
    }

    // This will only work for a vanilla HS256 token!
    // Key-pair hashed tokens (Cognito etc) will neeed their own check methods

    public function getJwtSignatureCheck($jwt, $asBase64 = false): bool
    {
        $parts = explode(".", $jwt);

        $header = json_decode(base64_decode($parts[0] ?? ""), true);
        $alg = $header['alg'] ?? "";

        if (empty($parts[2]) || !$alg == "HS256") {
            return false;
        }

        $signature = hash('sha256', $parts[0] . "." . ($parts[1] ?? ""));
        if ($asBase64) {
            $signature = $this->getBase64URL($signature);
        }

        return ($signature == ($parts[2] ?? null));
    }

    public function getJwtPayload($jwt): array
    {
        $parts = explode(".", $jwt);
        return json_decode(base64_decode($parts[1] ?? ""), true);
    }

    public function getAppFromJwtPayload($jwt): string
    {
        return $this->getJwtPayload($jwt)['client_id'] ?? "";
    }

    function getBase64URL($plainText): string
    {
        $base64 = base64_encode($plainText);
        $base64 = trim($base64, "=");
        $base64url = strtr($base64, '+/', '-_');
        return $base64url;
    }
}
