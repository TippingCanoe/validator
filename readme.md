# Validator

Validator makes defining centralized profiles of validation rules in Laravel 4 projects easy!

If you've ever written the same validator twice or wished that you could move the definition of a validator outside of your controller, consider using this tool.

## Usage

Using the Validator base class is easy.  Follow these steps & conventions to get the most out of it:

### Service Provider
Like with most Laravel 4 packages, this class requires that a service provider be configured so that the default factory and current request can be made available:

```
'TippingCanoe\Validator\ServiceProvider',
```

### Structure
Most projects will require several validation classes, so having somewhere clear to store them all is a good practice to get into.  Create the following directory structure in your project or library:

```
src/
	[YourProjectNamespace/]
		Validator/

```

Obviously substitute `[YourProjectNamespace]` with any depth of directories and ensure PSR-0 loading is enabled for the src/ directory.

### Subclassing

Using Validator is as simple as subclassing the `Base` class found in the `TippingCanoe\Validator` namespace and defining the desired rules and behaviours:


```
<?php namespace [YourProjectNamespace/]\Validator;

use TippingCanoe\Validator\Base;


class CommentStore extends Base {

	protected $rules = [
		'content' => 'required',
		'user_id' => 'exists:user'
	];

	protected $autoPopulate = true;

}
```

The property `$rules` follows exactly the same convention as set out in the [Laravel documentation for available validation rules](http://laravel.com/docs/validation#available-validation-rules).

When `$autoPopulate` is set to `true`, a validator instantiated without any values will attempt to initialize from the current request if available.


### Validating

Validator is able to accomodate every lifecycle for checking and reporting.

#### Exception-Based

This syntax is most useful when performing validations from within API controllers:

```
<?php namespace [YourProjectNamespace]\Controller;

use Illuminate\Routing\Controllers\Controller as Base;
use [YourProjectNamespace]\Validator\CommentStore as CommentStoreValidator;
use [YourProjectNamespace]\Model\Comment as CommentModel;


class Comment extends Base {

	public function store() {

		$commentData = new CommentStoreValidator();
		$commentData->assertValid();

		$comment = new CommentModel($commentData->values);

		// ...

	}

}	
```

This two-line setup ensures that you're writing a minimal amount of circumstantial code to perform the validation.  Elsewhere in your Laravel project, you can configure an exception handler to catch instances of `TippingCanoe\Validator\Exception` and produce standardized output.

#### Check-Based

This syntax is useful when you don't want to end the current controller context to provide validation feedback, most commonly when rendering template output:

```
<?php namespace [YourProjectNamespace]\Controller;

use Illuminate\Routing\Controllers\Controller as Base;
use [YourProjectNamespace]\Validator\CommentStore as CommentStoreValidator;

class Comment extends Base {

	public function store() {

		$commentData = new CommentStoreValidator();
		if(!$commentData->valid()) {
			// $commentData->errors
		}
		
		// ...
		
	}

}

```

## Contact

Validator is created by [Alexander Trauzzi](mailto:a.trauzzi@tippingcanoe.com) of [Tipping Canoe](http://www.tippingcanoe.com).

Feel free to get in touch or open bug/feature ticket.