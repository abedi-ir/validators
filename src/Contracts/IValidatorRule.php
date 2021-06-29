<?php
namespace Jalno\Validators\Contracts;

use Illuminate\Contracts\Validation\{Rule, ValidatorAwareRule, Validator as ValidatorContract};

interface IValidatorRule extends Rule, ValidatorAwareRule
{
	public static function extend(): void;
}
