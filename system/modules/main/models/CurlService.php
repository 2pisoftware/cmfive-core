<?php

/**
 * CURL helper static class
 * 
 * @author <adam@2pisoftware.com>
 */
class CurlService extends DbService {

	/**
	 * Creates and runs a get request and passes the response to a callback
	 * 
	 * @param string $url
	 * @param array $data
	 * @param Function $callback
	 */
	public static function getRequest($url, Array $data = [], $callback = null) {
		$handle = curl_init();
		
		curl_setopt($handle, CURLOPT_URL, $url . (!empty($data) && is_array($data) ? '?' . http_build_query($data) : ''));
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		
		$output = curl_exec($handle);
		$error = curl_error($handle);
		
		curl_close($handle);
		
		if (is_callable($callback)) {
			$callback($output, $error);
		}

		return $output;
	}
	
	/**
	 * Creates and runs a post request and passes the response to a callback
	 * 
	 * @param string $url
	 * @param array $data
	 * @param Function $callback
	 */
	public static function postRequest($url, Array $data = [], $callback = null) {
		$handle = curl_init();
		
		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_POST, 1);
		curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));

		// receive server response
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($handle);
		$error = curl_error($handle);

		curl_close($handle);
		
		if (is_callable($callback)) {
			$callback($output, $error);
		}

		return $output;
	}

    /**
	 * Creates and runs a post request and passes the response to a callback
	 * 
	 * @param string $url
	 * @param array $data
	 * @param Function $callback
	 */
	public static function postRequestWithAuth($url, string $username, string $password, Array $data = [], $callback = null) {
		$handle = curl_init();
		
		curl_setopt($handle, CURLOPT_URL, $url);
		curl_setopt($handle, CURLOPT_POST, 1);
		curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));

        curl_setopt($handle, CURLOPT_USERPWD, $username . ":" . $password);

		// receive server response
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($handle);
		$error = curl_error($handle);

		curl_close($handle);
		
		if (is_callable($callback)) {
			$callback($output, $error);
		}

		return $output;
	}
}
