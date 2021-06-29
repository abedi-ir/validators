<?php
namespace Jalno\Validators\Providers;

use Jalno\Validators\Rules;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

class ValidatorsServiceProvider extends ServiceProvider
{
	/**
     * @inheritdoc
     */
	public function boot()
	{
		$this->registerDefaultVlidators();
	}

	protected function registerDefaultVlidators()
	{
		foreach (array(
			Rules\CellphoneValidatorRule::class
		) as $validator) {
			$validator::extend();
		}
	}
}
