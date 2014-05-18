<?php namespace TippingCanoe\Validator;

use Illuminate\Support\ServiceProvider as Base;
use TippingCanoe\Validator\Base as BaseValidator;

class ServiceProvider extends Base {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {

		$this->package('tippingcanoe/validator');

		// Register the base implementations necessary.
		BaseValidator::setFactory($this->app['validator']);
		BaseValidator::setRequest($this->app['request']);

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register() {
	}

}