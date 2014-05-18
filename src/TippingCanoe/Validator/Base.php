<?php namespace TippingCanoe\Validator;

use ArrayAccess;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use TippingCanoe\Validator\Exception as ValidationException;


abstract class Base implements ArrayAccess {

	//
	// Static
	//

	/** @var Factory */
	private static $factory;
	/** @var Request */
	private static $request;

	/**
	 * Assign the Factory to use for creating validators.
	 *
	 * @param Factory $factory
	 */
	public static function setFactory(Factory $factory) {
		self::$factory = $factory;
	}

	/**
	 * Assign the request to use when retrieving/checking values.
	 *
	 * @param Request $request
	 */
	public static function setRequest(Request $request) {
		self::$request = $request;
	}

	/**
	 * Builds a new instances, offering strong typing because each subclass has this method.
	 *
	 * @param array $values
	 * @return static
	 */
	public static function make(array $values = null) {
		return new static($values);
	}

	//
	// Instance
	//

	/** @var array Rules to pass to the underlying Validator instance. */
	protected $rules = [];

	/** @var bool Whether we should auto-load values from the request. */
	protected $autoPopulate = false;

	/** @var \Illuminate\Validation\Validator */
	private $validator;

	/** @var array */
	public $values = [];
	/** @var array */
	public $errors;

	public function __construct(array $values = null) {

		// If no values were provided, grok them from the app.
		if($values)
			$this->values = $values;
		elseif($this->autoPopulate && self::$request)
			$this->values = self::$request->all();

	}

	/**
	 * Tests validation and throws an exception if it fails.
	 *
	 * @param bool $partial Whether a partial validation is okay.
	 * @throws Exception
	 * @return boolean Whether the current state of the validator is valid.
	 */
	public function assertValid($partial = false) {

		if($partial)
			$this->useFields(array_keys($this->values));

		if($this->valid()) return true;
		else throw new ValidationException($this->errors->toArray());

	}

	/**
	 * Returns whether the current state of the validator is valid.
	 *
	 * @param bool $partial Whether a partial validation is okay.
	 * @return bool
	 */
	public function valid($partial = false) {

		if($partial)
			$this->useFields(array_keys($this->values));

		// Generate our implementation from the app.
		$this->validator = self::$factory->make($this->values, $this->rules);

		if($this->validator->passes())
			return true;

		$this->errors = $this->validator->messages();

		return false;

	}

	/**
	 * Assigns the set of values to check.
	 *
	 * @param array $values
	 */
	public function setValues(array $values) {
		$this->values = $values;
	}

	/**
	 * Gets all values, excluding files.
	 *
	 * @return array
	 */
	public function values() {
		return array_except($this->values, self::$request->files->keys());
	}

	/**
	 * Retrieves a value from the validator, returning a configurable default if not present.
	 *
	 * @param string $offset
	 * @param null|mixed $default
	 * @return null|mixed
	 */
	public function get($offset, $default = null) {

		if(isset($this[$offset]))
			return $this[$offset];

		return $default;

	}

	/**
	 * Returns errors for a specific field.
	 *
	 * @param string $offset
	 * @param null|mixed $default
	 * @return mixed
	 */
	public function getErrorsFor($offset, $default = null) {
		return $this->errors->get($offset, $default);
	}

	/**
	 * Returns only the files for the current request.
	 *
	 * @return array
	 */
	public function files() {
		return self::$request->files->all();
	}

	/**
	 * Returns whether the specified offset is set.
	 *
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->values);
	}

	/**
	 * Returns the value for the specified offset.
	 *
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		return $this->values[$offset];
	}

	/**
	 * Assigns a value to the specified offset.
	 *
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		$this->values[$offset] = $value;
	}

	/**
	 * Removes the value and key for the specified offset.
	 *
	 * @param mixed $offset
	 */
	public function offsetUnset($offset) {
		unset($this->values[$offset]);
	}

	/**
	 * Returns only the values for the indicated fields.
	 *
	 * @param array $fields
	 * @return array
	 */
	public function only(array $fields) {
		return array_only($this->values, $fields);
	}

	/**
	 * Alters the validator to only validate the specified fields.
	 *
	 * @param array|string $fields
	 */
	public function useFields($fields) {

		if(!is_array($fields))
			$fields = func_get_args();

		$this->rules = array_only($this->rules, $fields);

	}

}