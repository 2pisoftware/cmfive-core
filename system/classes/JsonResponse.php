<?php

class JsonResponse {

	public $status = 200;
	public $success = false;
	public $message = '';
	public $data = [];

	public function setStatus(int $status) {
		$this->status = $status;
		return $this;
	}

	public function setSuccess(bool $success) {
		$this->success = $success;
		return $this;
	}

	public function setMessage(string $message) {
		$this->message = $message;
		return $this;
	}

	public function setData($data) {
		$this->data = $data;
		return $this;
	}

	public function setSuccessfulResponse($message, $data) {
		$this->status = 200;
		$this->success = true;
		$this->message = $message;
		$this->data = $data;
		return $this;
	}

	public function __toString() {
		return json_encode($this);
	}

}