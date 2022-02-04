<?php

class HttpRequest
{

    public $url = '';
    public $data = [];
    public $method = 'GET';

    private $curl_handle;

    public function __construct($url, $method = 'GET', $data = [])
    {
        $this->curl_handle = curl_init();

        $this->url = $url;
        $this->method = $method;
        $this->data = $data;

        switch (strtoupper($method)) {
            case 'POST': {
                    curl_setopt($this->curl_handle, CURLOPT_URL, $url);
                    curl_setopt($this->curl_handle, CURLOPT_POST, 1);
                    curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $this->data);
                    break;
            };
            case 'DELETE':
                curl_setopt_array($this->curl_handle, [
                    CURLOPT_URL => $url,
                    CURLOPT_CUSTOMREQUEST => "DELETE"
                ]);
                break;
            case 'GET':
            default:
                curl_setopt($this->curl_handle, CURLOPT_URL, $url . (!empty($data) && is_array($data) ? '?' . http_build_query($data) : ''));
        }
        curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);

        return $this;
    }

    public function __destruct()
    {
        curl_close($this->curl_handle);
    }

    public function setOpt($callback = null)
    {
        if (is_callable($callback)) {
            $callback($this->curl_handle);
        }

        return $this;
    }

    /**
     * Adds Basic Authenication to the request.
     *
     * @param string $username
     * @param string $password
     * @return void
     */
    public function setBasicAuth($username = "", $password = "")
    {
        curl_setopt($this->curl_handle, CURLOPT_USERPWD, $username . ":" . $password);
    }

    /**
     * Executes the request and returns the response data, status code & error message.
     *
     * @return array[string]
     */
    public function execute()
    {
        $data = curl_exec($this->curl_handle);
        $status_code = curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE);
        $error_message = curl_error($this->curl_handle);

        return ["status_code" => $status_code, "data" => $data, "error" => $error_message];
    }
}
