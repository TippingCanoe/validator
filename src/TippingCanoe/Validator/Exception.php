<?php namespace TippingCanoe\Validator;

use Exception as BaseException;


class Exception extends BaseException {

	/**
	 * @var string
	 */
	protected $messages;

	/**
	 * @param array $messages
	 */
	public function __construct(array $messages = null) {

		if($messages) {
			$this->setMessages($messages);
			$this->message = array_flatten($messages)[0];
		}

	}

	public function setMessages(array $messages) {
		$this->messages = $messages;
		$this->message = array_flatten($messages)[0];
	}

	public function getMessages() {
		return $this->messages;
	}

}