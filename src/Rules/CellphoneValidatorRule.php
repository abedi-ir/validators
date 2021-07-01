<?php
namespace Jalno\Validators\Rules;

use Jalno\Validators\Support\Safe;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Jalno\Validators\{Contracts\IValidatorRule, Support};
use Illuminate\Contracts\Validation\{Rule, ValidatorAwareRule, Validator as ValidatorContract};

class CellphoneValidatorRule implements IValidatorRule
{

    public static function extend(): void
    {
        Validator::extend("cellphone", function(string $attribute, $value, array $parameters, ValidatorContract $validator) {
            return (new CellphoneValidatorRule)->setValidator($validator)->passes($attribute, $value);
        });
    }

    /**
     * @return string
     */
    public static function getDefaultCountryCode(): string
    {
        if (empty(self::$defaultCountryCode)) {
            self::$defaultCountryCode = config("jalno.validators.cellhone_validator.default_cellphone_country_code", "IR");
        }

        return self::$defaultCountryCode;
    }

    /**
     * @property string $defaultCountryCode that is default country in ISO 3166-1 alpha-2 format
     */
    protected static string $defaultCountryCode = "";

    protected ?ValidatorContract $validator;

    /**
     * @var array<array<string,string>|string>
     */
    protected array $values = [];

    protected bool $combinedOutput = true;

    /**
     * @inheritdoc
     * @param ValidatorContract $validator
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * @inheritdoc
     * 
     * @param array{code:string,number:string}|string|string[] $value
     */
    public function passes($attribute, $value)
    {
        if (is_string($value)) {
            if (strpos($value, '.') !== false) {
                $parts = explode('.', $value);
                $code = $parts[0];
                // check if code is numeric, we find the related region code if just one region exists for the code
                if (is_numeric($code)) {
                    $relatedCountries = Geo\CountryCodeToRegionCodeMap::$CC2RMap[$code] ?? [];
                    if ($relatedCountries and count($relatedCountries) == 1) {
                        $code = $relatedCountries[0];
                    }
                }
                $value = array(
                    'code' => $code,
                    'number' => $parts[1],
                );
            } else {
                $value = array(
                    'code' => '',
                    'number' => $value,
                );
            }
        }
        if (!is_array($value)) {
            throw ValidationException::withMessages([$attribute => "The cellphone must be an array."]);
        }
        if (count($value) != 2 or !isset($value['code'], $value['number'])) {
            throw ValidationException::withMessages([$attribute => "The cellphone data must contains code and number indexes."]);
        }

        $value = array_map('trim', $value);
        $value['code'] = strtoupper($value['code']);
        $value['number'] = ltrim($value['number'], '0');

        if (empty($value['code'])) {
            $value['code'] = self::getDefaultCountryCode();
        }
        if (!is_string($value['code'])) {
            throw ValidationException::withMessages(["{$attribute}.code" => "The selected {$attribute}.code is invalid."]);
        }
        if (!is_numeric($value['number'])) {
            throw ValidationException::withMessages(["{$attribute}.number" => "The selected {$attribute}.number must be a numeric."]);
        }

        $regionCodeToCountryCode = Geo\CountryCodeToRegionCodeMap::regionCodeToCountryCode();
        if (!array_key_exists($value['code'], $regionCodeToCountryCode)) {
            throw ValidationException::withMessages(["{$attribute}.code" => "The selected {$attribute}.code is invalid."]);
        }

        switch ($value['code']) {
            /**
             * Iran, Islamic Republic Of
             */
            case 'IR':
                if (!Safe::isCellphoneIR((substr($value['number'], 0, 2) !== "98" ? "98" : "") . $value['number'])) {
                    throw ValidationException::withMessages(["{$attribute}" => "The selected {$attribute} is not valid IR cellphone."]);
                }
                $value["number"] = Safe::cellphoneIR($value["number"]);
                break;
        }

        $combinedData = $value['code'] . '.' . $value['number'];

        if ($this->values) {
            $found = false;
            foreach ($this->values as $item) {
                if (is_string($item)) {
                    if ($item == $combinedData) {
                        $found = true;
                        break;
                    }
                } elseif (is_array($item) and isset($item['code'], $item['number'])) {
                    if ($item['code'] == $value['code'] and $item['number'] == $value['number']) {
                        $found = true;
                        break;
                    }
                }
            }
            if (!$found) {
                throw ValidationException::withMessages(["{$attribute}" => "The selected {$attribute} is invalid."]);
            }
        }

        if ($this->validator and method_exists($this->validator, "setData")) {
            $this->validator->setData(array(
                $attribute => $this->combinedOutput ? $combinedData : array(
                    'code' => $value['code'],
                    'number' => $value['number'],
                ),
            ));
        }

        return true;
    }

    /**
     * @inheritdoc
     * 
     * @return string|string[]|array<string,string>
     */
    public function message()
    {
        return 'The :attribute is invalid.';
    }

    /**
     * @param string[]|array{"code":string,"number":string} $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function setCombinedOutput(bool $val): void
    {
        $this->combinedOutput = $val;
    }
}
