<?php

class HttpRequest {
	
	public $url = '';
	public $data = [];
	public $method = 'GET';

	private $curl_handle;

	public function __construct($url, $method = 'GET', $data = []) {
		$this->curl_handle = curl_init();
		
		$this->url = $url;
		$this->method = $method;
		$this->data = $data;

		switch(strtoupper($method)) {
			case 'POST': {
				curl_setopt($this->curl_handle, CURLOPT_URL, $url);
				curl_setopt($this->curl_handle, CURLOPT_POST, 1);
				curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $this->data);
				break;
			};
			case 'GET':
			default:
				curl_setopt($this->curl_handle, CURLOPT_URL, $url . (!empty($data) && is_array($data) ? '?' . http_build_query($data) : ''));
		}
		curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);

		return $this;
	}

	public function __destruct() {
		curl_close($this->curl_handle);
	}

	public function setOpt($callback = null) {
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
	public function setBasicAuth($username = "", $password = "") {
		curl_setopt($this->curl_handle, CURLOPT_USERPWD, $username . ":" . $password);
	}

	/**
	 * Executes the request with an optional error parameter.
	 *
	 * @param string $error
	 * @return string
	 */
	public function execute(string &$error = "") {
		$output = curl_exec($this->curl_handle);
		$error = curl_error($this->curl_handle);

		return $output;
	}

	/**
	 * Runs the request.
	 *
	 * @deprecated 2.14.5 - Use simplified function execute instead.
	 *
	 * @param string $output
	 * @param string $error
	 * @return string
	 */
	public function run(&$output, &$error) {
		$output = $this->execute($error);
		return $output;
	}
}