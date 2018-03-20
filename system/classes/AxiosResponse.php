<?php

/**
 * Helper class to respond to Axios.JS AJAX requests, it's different to normal
 * AJAX in that the library will set 'status', 'statusText', 'data' variables itself
 */
class AxiosResponse extends JsonResponse {

	public function __toString() {
		http_response_code($this->status);
		
		return json_encode($this->data);
	}

}