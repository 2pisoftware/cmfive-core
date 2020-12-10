<?php

/**
 * Helper class to respond to Axios.JS AJAX requests, it's different to normal
 * AJAX in that the library will set 'status', 'statusText', 'data' variables itself
 */
class AxiosResponse extends JsonResponse
{
    /**
     * Converts class into a json encoded response for the Axios AJAX library
     *
     * @return string
     */
    public function __toString(): string
    {
        http_response_code($this->status);

        // If you're not getting any response, this is most likely why
        $json = json_encode($this->data);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $json;
        } else {
            return '';
        }
    }
}
