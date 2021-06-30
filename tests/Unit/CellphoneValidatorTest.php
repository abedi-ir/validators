<?php

use Illuminate\Validation\Validator;
use Jalno\Validators\Rules\CellphoneValidatorRule;

class CellphoneValidatorTest extends TestCase
{
    public function testStringCelphones(): void
    {
        CellphoneValidatorRule::extend();
        $rule = new CellphoneValidatorRule();
        $app = $this->createApplication();
        $rule->setValidator(new Validator($app->make("translator"), array(
            "cellphone" => array(
                "code" => "IR",
                "number" => "09136481798",
            ),
        ), [], [], []));
        $this->assertTrue($rule->passes("cellphone", "09136481798"));
        $rule->setValues([
            "IR.9136481799",
            [
                "code" => "IR",
                "number" => "9136481798",
            ]
        ]);
        $rule->setCombinedOutput(false);
        $this->assertTrue($rule->passes("cellphone", "09136481798"));
        $this->assertTrue($rule->passes("cellphone", "IR.09136481798"));
        $this->assertTrue($rule->passes("cellphone", "98.09136481798"));
    }
}

