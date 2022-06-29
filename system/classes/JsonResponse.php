<?php

class JsonResponse
{
    public $status = 200;
    public $success = false;
    public $message = '';
    public $data = [];

    /**
     * Sets status code that will be set in the header
     *
     * @param integer $status
     * @return self
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Sets whether or not the request was successful
     *
     * @param boolean $success
     * @return self
     */
    public function setSuccess(bool $success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * Sets message to be sent to the client
     *
     * @param string $message
     * @return self
     */
    public function setMessage(?string $message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Sets data to be sent to the client
     *
     * @param mixed $data
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Shorthand function for creating a successful response
     *
     * @param string $message
     * @param mixed $data
     * @return self
     */
    public function setSuccessfulResponse(?string $message, $data)
    {
        $this->status = 200;
        $this->success = true;
        $this->message = $message;
        $this->data = $data;
        return $this;
    }

    /**
     * Shorthand function for creating a not found (404) response
     *
     * @param string $message
     * @return self
     */
    public function setNotFoundResponse(?string $message)
    {
        $this->status = 404;
        $this->success = false;
        $this->message = $message;
        $this->data = null;
        return $this;
    }

    /**
     * Shorthand function for creating a server error response
     *
     * @param string $message
     * @param mixed $data
     * @return self
     */
    public function setErrorResponse(?string $message, $data)
    {
        $this->status = 500;
        $this->success = false;
        $this->message = $message;
        $this->data = $data;
        return $this;
    }

    /**
     * Converts class into a json encoded response
     *
     * @return string
     */
    public function __toString(): string
    {
        header('Content-Type: application/json');
        http_response_code($this->status);

        return json_encode($this);
    }
}
