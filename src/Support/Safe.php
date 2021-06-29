<?php
namespace Jalno\Validators\Support;

class Safe
{
    public static function isCellphoneIR(string $cellphone): bool
    {
        $length = strlen($cellphone);
        if (($length == 10 and substr($cellphone, 0, 1) == '9') or // 9131101234
            ($length == 11 and substr($cellphone, 0, 2) == '09') or // 09131101234
            ($length == 12 and substr($cellphone, 0, 3) == '989') or // 989131101234
            ($length == 13 and substr($cellphone, 0, 4) == '9809') or // 9809131101234
            ($length == 13 and substr($cellphone, 0, 4) == '+989') or // +989131101234
            ($length == 14 and substr($cellphone, 0, 5) == '98989')) // 98989131101234
        {
            $sub4 = '';
            switch ($length) {
                case(10): // 913
                    $sub4 = '0' . substr($cellphone, 0, 3);
                    break;
                case(11): // 0913
                    $sub4 = substr($cellphone, 0, 4);
                    break;
                case(12): // 98913
                    $sub4 = '0' . substr($cellphone, 2, 3);
                    break;
                case(13): // 9809 || +98913
                    if (substr($cellphone, 0, 4) == '9809') {
                        $sub4 = substr($cellphone, 2, 4);
                    } else if (substr($cellphone, 0, 4) == '+989') {
                        $sub4 = '0' . substr($cellphone, 3, 3);
                    }
                    break;
                case(14): // 9898913
                    $sub4 = '0' . substr($cellphone, 4, 3);
                    break;
            }
            switch ($sub4) {
                case('0910'):case('0911'):case('0912'):case('0913'):case('0914'):case('0915'):case('0916'):case('0917'):case('0918'):case('0919'):case('0990'):case('0991'):case('0992'):case('0993'):case('0994'): // TCI
                case('0930'):case('0933'):case('0935'):case('0936'):case('0937'):case('0938'):case('0939'): // IranCell
                case('0901'):case('0902'):case('0903'):case('0904'):case('0905'):case('0941'): // IranCell - ISim
                case('0920'):case('0921'):case('0922'): // RighTel
                case('0931'): // Spadan
                case('0932'): // Taliya
                case('0934'): // TKC
                case('0998'): // ShuttleMobile
                case('0999'): // Private Sector: ApTel, Azartel, LOTUSTEL, SamanTel
                    return true;
                default:
                    return false;
            }
        }
        return false;
    }

    public static function cellphoneIR(string $cellphone): ?string
    {
        $length = strlen($cellphone);
        if (($length == 10 and substr($cellphone, 0, 1) == '9') or // 9131101234
            ($length == 11 and substr($cellphone, 0, 2) == '09') or // 09131101234
            ($length == 12 and substr($cellphone, 0, 3) == '989') or // 989131101234
            ($length == 13 and substr($cellphone, 0, 4) == '9809') or // 9809131101234
            ($length == 13 and substr($cellphone, 0, 4) == '+989') or // +989131101234
            ($length == 14 and substr($cellphone, 0, 5) == '98989')) // 98989131101234
        {
            return substr($cellphone, $length - 10);
        }
        return null;
    }
}
