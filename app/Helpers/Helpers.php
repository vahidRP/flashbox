<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;

class Helpers
{
    public static function FarsiNumber($string)
    {
        $farsi_array = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english_array = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($english_array, $farsi_array, $string);
    }

    public static function EnglishNumber($string)
    {
        $farsi_array = ['۰', '٠', '۱', '١', '۲', '٢', '۳', '٣', '۴', '٤', '۵', '٥', '۶', '۷', '۸', '۹', '٩'];
        $english_array = ['0', '0', '1', '1', '2', '2', '3', '3', '4', '4', '5', '5', '6', '7', '8', '9', '9'];

        return str_replace($farsi_array, $english_array, $string);
    }

    public static function normalizeAll($inputs, $excepts = [])
    {
        foreach($inputs as $inputName => $inputValue){
            $inputs[$inputName] = self::normalize($inputValue, $inputName, $excepts);
            if($inputName === 'password' && empty($inputs[$inputName])){
                unset($inputs[$inputName]);
            }
        }

        return $inputs;
    }

    public static function normalize($value, $inputName = null, $excepts = [])
    {
        if(!empty($value)){
            if(!($value instanceof UploadedFile)){
                $excepts[] = 'password';
                if(!in_array($inputName, $excepts) && !is_array($value)){
                    $value = self::EnglishNumber(trim(preg_replace('!\s+!', ' ', $value)));
                    $value = str_replace('ي', 'ی', $value);
                    if($value == 'true' || $value == 'false'){
                        $value = $value == 'true' ? 1 : 0;
                    }
                }

                if($inputName == 'email' || $inputName == 'username'){
                    $value = strtolower($value);
                }
            }
        }else{
            $value = null;
        }

        return $value;
    }

    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}
