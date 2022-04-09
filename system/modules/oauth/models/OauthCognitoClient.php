<?php


use Aws\Exception\AwsException as AwsException;

class OauthCognitoClient extends DbService
{
    public $_system;
    public $_logging = [];
    public $_critical = false;
    public $_tokenIssuer;

    public function makeWarningsCritical()
    {
        if (!$this->_critical) {
            set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            });
        }
        $this->_critical = true;
    }

    public function makeWarningsSafe()
    {
        if ($this->_critical) {
            restore_error_handler();
        }
        $this->_critical = false;
    }

    public function getTokenIssuer($provider, $appAuth = null)
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        if (!empty($appAuth)) {
            $headers['Authorization'] = "Basic " . $appAuth;
        }
        try {
            $this->_tokenIssuer = new GuzzleHttp\Client([
                'base_uri' => $provider,
                'headers' => $headers,
            ]);

            return $this->_tokenIssuer;
        } catch (Exception $ex) {
            if (is_a($ex, "GuzzleHttp\Exception\ClientException")) {
                $results = "Exception occurred, " . $ex->getMessage();
            } else $results = "Internal or network error";
            $this->failHandler(['From' => "Cognito Token Issuer", 'Failed' => "Connection", 'Info' => $results]);
            return null;
        }
    }

    public function getIssuedToken($param = [])
    {
        if (is_null($this->_tokenIssuer)) {
            $this->failHandler(['From' => "Cognito Token Issuer", 'Failed' => "Requesting", 'Info' => "Connection non-existent"]);
            return null;
        }

        try {
            $connection = $this->_tokenIssuer;
            $response = $connection->request(
                'POST',
                '',
                [
                    'form_params' => $param
                ]
            );

            return $response->getBody()->getContents();
        } catch (Exception $ex) {
            if (is_a($ex, "GuzzleHttp\Exception\ClientException")) {
                $results = "Exception occurred, " . $ex->getMessage();
            } else {
                $results = "Internal or network error";
            }
            $this->failHandler(['From' => "Cognito Token Issuer", 'Failed' => "Reading", 'Info' => $results]);
            return null;
        }
    }

    public function getSystem()
    {
        // try {
        //     $this->makeWarningsCritical();

        //     // get aws client system
        //     $LambdaClient = LambdaClient::factory(
        //         [
        //             // expect we will catch an IAM from the host!
        //             // 'profile' => 'default',
        //             'region' => Config::get('cpi.aws.region'), //'ap-southeast-2',
        //             'version' => 'latest',
        //             'credentials' => null,
        //         ]
        //     );

        //     $this->makeWarningsSafe();

        //     if (empty($LambdaClient)) {
        //         return null;
        //     }
        //     $this->_system = $LambdaClient;
        //     return $this->_system;
        // } catch (Exception $ex) {
        //     $this->makeWarningsSafe();
        //     $results = "Internal, network or access error";
        //     $this->failHandler(['From' => "Lambda", 'Failed' => "Configuration", 'Info' => $results]);
        //     return null;
        // }
    }


    public function failHandler($details = null)
    {
        $message = "Unascribed failure occured in AWS Cognito service.";

        if (is_array($details)) {
            $From = isset($details['From']) ? $details['From'] : "";
            $Failed = isset($details['Failed']) ? $details['Failed'] : "";
            $Info = isset($details['Info']) ? $details['Info'] : "";

            $message = "Oauth Module (" . $From . ") failed for " . $Failed . ": " . $Info;
        }
        $this->w->Log->error($message);
        $this->_logging[] = $message;
    }

    public function failCount()
    {
        return count($this->_logging);
    }

    public function failMailer($email)
    {
        $to = $email;
        $replyto = Config::get('main.company_support_email');
        $subject = "Cognito oauth system error.";
        $message = "The following problems were logged: <br> \n";
        foreach ($this->_logging as $logged) {
            $message .= $logged . " <br> \n";
        }
        $this->w->Mail->sendMail($to, $replyto, $subject, $message);
    }
}
