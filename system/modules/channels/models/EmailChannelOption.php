<?php

use \Laminas\Mail\Message as LaminasMailMessage;

//
// The purpose of this class is to expose protocol
//
class ZendMailStorageImap extends \Laminas\Mail\Storage\Imap
{
    public $protocol;

    public function __construct($params)
    {
        if (is_array($params)) {
            $params = (object) $params;
        }

        $this->has['flags'] = true;

        if ($params instanceof \Laminas\Mail\Protocol\Imap) {
            $this->protocol = $params;
            try {
                $this->selectFolder('INBOX');
            } catch (\Exception $e) {
                throw new \Laminas\Mail\Exception\RuntimeException('cannot select INBOX, is this a valid transport?', 0, $e);
            }
            return;
        }

        if (!isset($params->user)) {
            throw new \Laminas\Mail\Exception\InvalidArgumentException('need at least user in params');
        }

        $host     = isset($params->host)     ? $params->host     : 'localhost';
        $password = isset($params->password) ? $params->password : '';
        $port     = isset($params->port)     ? $params->port     : null;
        $ssl      = isset($params->ssl)      ? ($port !== 993 ? "tls" : $params->ssl) : false;
        $options  = isset($params->options)  ? $params->options  : null;

        $this->protocol = new ZendMailProtocolImap();
        $this->protocol->connect($host, $port, $ssl, $options);
        if (!$this->protocol->login($params->user, $password)) {
            throw new \Laminas\Mail\Exception\RuntimeException('cannot login, user or password wrong');
        }
        $this->selectFolder(isset($params->folder) ? $params->folder : 'INBOX');
    }
}

use Laminas\Stdlib\ErrorHandler;

class ZendMailProtocolImap extends \Laminas\Mail\Protocol\Imap
{

    public function connect($host, $port = null, $ssl = false, $options = [])
    {
        $isTls = false;

        if ($ssl) {
            $ssl = strtolower($ssl);
        }

        switch ($ssl) {
            case 'ssl':
                $host = 'ssl://' . $host;
                if (!$port) {
                    $port = 993;
                }
                break;
            case 'tls':
                $isTls = true;
                // break intentionally omitted
            default:
                if (!$port) {
                    $port = 143;
                }
        }

        ErrorHandler::start();

        // Use stream_context_create instead of fsockopen as it allows us to specify SSL stream options
        $stream = stream_context_create($options);
        // if ($ssl !== false && !is_null($options) && is_array($options) && array_key_exists('ssl', $options)) {
        //     stream_context_set_option($stream, $options);
        // }

        $this->socket = stream_socket_client($host . ':' . $port, $errno, $errstr, self::TIMEOUT_CONNECTION, STREAM_CLIENT_CONNECT, $stream);

        $error = ErrorHandler::stop();
        if (!$this->socket) {
            throw new \Laminas\Mail\Exception\RuntimeException(sprintf(
                'cannot connect to host %s',
                ($error ? sprintf('; error = %s (errno = %d )', $error->getMessage(), $error->getCode()) : '')
            ), 0, $error);
        }

        if (!$this->assumedNextLine('* OK')) {
            throw new \Laminas\Mail\Exception\RuntimeException('host doesn\'t allow connection');
        }

        if ($isTls) {
            $result = $this->requestAndResponse('STARTTLS');
            $result = $result && stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            if (!$result) {
                throw new \Laminas\Mail\Exception\RuntimeException('cannot enable TLS');
            }
        }
    }
}

class EmailChannelOption extends DbObject
{
    static $_db_table = "channel_email_option";
    public $_channeltype = "email";
    public $channel_id;
    public $server;
    public $s_username;
    public $s_password;
    public $port;
    public $use_auth;
    public $protocol; // pop3, imap
    static public $_select_protocol = ["POP3", "IMAP"];
    public $subject_filter;
    public $to_filter;
    public $from_filter;
    public $cc_filter;
    public $body_filter;
    public $folder;
    public $post_read_action; // delete, mark as archived, move to folder, apply tag, forward to email
    static public $_select_read_action = ["Archive", "Move to Folder", "Apply Tag", "Forward to", "Delete"];
    public $post_read_parameter; // stores extra data, eg. tag name, folder name, forward email, etc.

    public $verify_peer;
    public $allow_self_signed;

    public function __construct(Web $w)
    {
        parent::__construct($w);
        $this->setPassword(hash("md5", $w->moduleConf("channels", "__password")));
    }

    public function delete($force = false)
    {
        $channel = $this->getChannel();
        $channel->delete($force);

        parent::delete($force);
    }

    public function getChannel()
    {
        if (!empty($this->channel_id)) {
            return ChannelService::getInstance($this->w)->getChannel($this->channel_id);
        }
        return null;
    }

    public function getNotifyUser()
    {
        $channel = $this->getChannel();
        if (!empty($channel)) {
            return $channel->getNotifyUser();
        }
    }

    public function read()
    {
        // Setup filter array
        $filter_arr = [];

        if (!empty($this->to_filter)) {
            $filter_arr[] = "TO " . $this->to_filter;
        }

        if (!empty($this->from_filter)) {
            $filter_arr[] = "FROM " . $this->from_filter;
        }

        if (!empty($this->cc_filter)) {
            $filter_arr[] = "CC " . $this->cc_filter;
        }

        if (!empty($this->subject_filter)) {
            $filter_arr[] = "SUBJECT " . $this->subject_filter;
        }

        if (!empty($this->body_filter)) {
            $filter_arr[] = "BODY " . $this->body_filter;
        }

        $filter_arr[] = "UNSEEN";

        // Connect and fetch emails
        LogService::getInstance($this->w)->setLogger('EmailChannel')->info("Connecting to mail server");
        list($connected, $mail) = $this->connectToMail();

        if ($connected) {
            LogService::getInstance($this->w)->setLogger('EmailChannel')->info("Getting messages with filter: " . json_encode($filter_arr));
            $results = $mail->protocol->search($filter_arr);

            if (!empty($results)) {
                LogService::getInstance($this->w)->setLogger('EmailChannel')->info("Found " . count($results) . " messages, looping through");

                foreach ($results as $index => $messagenum) {
                    $i = $index + 1;
                    LogService::getInstance($this->w)->setLogger('EmailChannel')->debug("Reading message {$i}");
                    $message = $mail->getMessage($messagenum);

                    /**create a regular zend_mail_message to use toString()
                     * this is required because the storage mail class doesn't support this method
                     */
                    $zend_message = new LaminasMailMessage();
                    $zend_message->setHeaders($message->getHeaders());
                    $zend_message->setBody($message->getContent());

                    $rawmessage = "";
                    try {
                        $rawmessage = $zend_message->toString();
                    } catch (Throwable $t) {
                        $rawmessage = $message->getContent();
                    }

                    $email = new EmailStructure();
                    $email->to = $message->to;

                    $email->message_id = $message->getHeader('Message-Id')->getFieldValue();

                    // get the from address, only expecting one
                    foreach ($zend_message->getFrom() as $address) {
                        $email->from = $address->getName();
                        $email->from_email_address = $address->getEmail();
                        break;
                    }

                    $email->subject = $zend_message->getSubject();

                    // Create  ChannelMessages
                    $channel_message = new ChannelMessage($this->w);
                    $channel_message->channel_id = $this->channel_id;
                    $channel_message->message_type = "email";
                    $channel_message->is_processed = 0;
                    $channel_message->insert();

                    if ($message->isMultipart()) {
                        foreach (new RecursiveIteratorIterator($message) as $part) {
                            try {
                                $contentType = strtok($part->contentType, ';');
                                $transferEncoding = '';

                                if ($part->getHeaders()->has('ContentTransferEncoding')) {
                                    $transferEncoding = $part->getHeader("Content-Transfer-Encoding")->getFieldValue("transferEncoding");
                                }

                                switch ($contentType) {
                                    case "text/plain":
                                        $email->body["plain"] = trim($part->__toString());

                                        if ($transferEncoding == "base64") {
                                            $email->body['plain'] = base64_decode($email->body['plain']);
                                        } elseif ($transferEncoding == "quoted-printable") {
                                            $email->body['plain'] = quoted_printable_decode($email->body['plain']);
                                        }
                                        break;
                                    case "text/html":
                                        $email->body["html"] = trim($part->__toString());

                                        if ($transferEncoding == "base64") {
                                            $email->body['html'] = base64_decode($email->body['html']);
                                        } elseif ($transferEncoding == "quoted-printable") {
                                            $email->body['html'] = quoted_printable_decode($email->body['html']);
                                        }
                                        break;
                                    default:
                                        // Is probably an attachment so just save it
                                        $content_type_header = $part->getHeader("Content-Type");

                                        // Name is stored under "parameters" in an array
                                        $nameArray = $content_type_header->getParameters();
                                        $name = '';

                                        if (empty($nameArray) || !is_array($nameArray) || !array_key_exists('name', $nameArray)) {
                                            $content_dispositon = $part->getHeader('Content-Disposition');
                                            $content_dispositon_array = explode(';', $content_dispositon->getFieldValue('filename'));

                                            if (!empty($content_dispositon_array)) {
                                                foreach ($content_dispositon_array as $cda) {
                                                    $arr = explode('=', $cda);
                                                    if (trim($arr[0]) === 'filename') {
                                                        $name = trim($arr[1]);
                                                    }
                                                }
                                            }

                                            // adding check for long file names which are cut from content_disposition
                                            if (empty($name)) {
                                                $top_lines = trim($part->getTopLines());

                                                $name = $top_lines;
                                            }
                                        } else {
                                            $name = $nameArray['name'];
                                        }

                                        if (empty($name)) {
                                            $name = "attachment_" . substr(uniqid('', true), -6);
                                        }

                                        // Try and trim quotes off the name
                                        $name = trim($name, '"');
                                        $name = trim($name, "'");

                                        FileService::getInstance($this->w)->saveFileContent(
                                            $channel_message,
                                            ($transferEncoding == "base64" ? base64_decode(trim($part->__toString())) : trim($part->__toString())),
                                            $name,
                                            "channel_email_attachment",
                                            $contentType
                                        );
                                }
                            } catch (\Exception $e) {
                                LogService::getInstance($this->w)->setLogger('EmailChannel')->error("Zend_Mail_Exception {$e}");
                            }
                        }
                    } else {
                        $contentType = strtok($message->contentType, ';');
                        $transferEncoding = '';

                        if ($message->getHeaders()->has('ContentTransferEncoding')) {
                            $transferEncoding = $message->getHeader("Content-Transfer-Encoding")->getFieldValue("transferEncoding");
                        }

                        switch ($contentType) {
                            case "text/plain":
                            default:
                                $email->body["plain"] = trim($message->__toString());

                                if ($transferEncoding == "base64") {
                                    $email->body['plain'] = base64_decode($email->body['plain']);
                                } elseif ($transferEncoding == "quoted-printable") {
                                    $email->body['plain'] = quoted_printable_decode($email->body['plain']);
                                } else {
                                    $email->body['plain'] = $message->getContent();
                                }
                                break;
                        }
                    }
                    FileService::getInstance($this->w)->saveFileContent($channel_message, serialize($email), "email.txt", "channel_email_raw", "text/plain", 'serialized EmailStructure object | NOT SENT TO CLIENT');
                    FileService::getInstance($this->w)->saveFileContent($channel_message, $rawmessage, "rawemail.txt", "channel_email_raw", "text/plain", "raw email message | NOT SENT TO CLIENT");
                }
            } else {
                LogService::getInstance($this->w)->setLogger('EmailChannel')->info("No new messages found");
            }
        }
    }

    public function connectToMail($shouldDecrypt = true)
    {
        if ($shouldDecrypt) {
            $this->decrypt();
        }

        try {
            // Open email connection
            $options = null;

            if (!is_null($this->verify_peer)) {
                $options = [
                    'ssl' => ['verify_peer' => $this->verify_peer ? true : false]
                ];

                if (!is_null($this->allow_self_signed)) {
                    $options['ssl']['allow_self_signed'] = $this->allow_self_signed ? true : false;
                }
            }

            $mail = new ZendMailStorageImap(
                [
                    'host' => $this->server,
                    'user' => $this->s_username,
                    'port' => $this->port,
                    'password' => $this->s_password,
                    'ssl' => ($this->use_auth == 1 ? "SSL" : false),
                    'options' => $options
                ]
            );
            return [true, $mail];
        } catch (Exception $e) {
            LogService::getInstance($this->w)->setLogger('EmailChannel')->error("Error connecting to mail server: " . $e->getMessage());
            return [false, $e->getMessage()];
        }
    }

    public function getFolderList($shouldDecrypt = true)
    {
        $mail = $this->connectToMail($shouldDecrypt);
        $folders = [];

        if (!empty($mail)) {
            if ($mail) {
                foreach ($mail->getFolders() as $mailfolder) {
                    foreach ($mailfolder as $folder) {
                        $folders[] = $folder->__toString();
                    }
                }
            }
        }

        return $folders;
    }
}
