<?php

use Jalno\Validators\Support\Safe;
use Illuminate\Support\Facades\Validator;

class SafeTest extends TestCase
{
    public function testIRCellphones(): void
    {
		foreach (["9131101234", "09301101234", "989211101234", "9809311101234", "+989321101234", "98989341101234", "+989981101234", "+989991101234"] as $item) {
			$this->assertTrue(Safe::isCellphoneIR($item));
		}

		$this->assertFalse(Safe::isCellphoneIR("091311012345"));
		$this->assertFalse(Safe::isCellphoneIR("09001101234"));

		foreach (["9131101234", "09131101234", "989131101234", "9809131101234", "+989131101234", "98989131101234"] as $item) {
			$this->assertEquals(Safe::cellphoneIR($item), "9131101234");
		}

		$this->assertNull(Safe::cellphoneIR("091311012345"));
    }
}

