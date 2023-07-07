<?php
namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        $code       = config('code.apiValidationError');
        $message    = json_encode(implode('<br>', $validator->errors()->all()), JSON_UNESCAPED_UNICODE);
        $message    = str_replace('"', '', $message);
        $statusCode = 400;
        throw (new HttpResponseException(response()->json([
            'code'      => $code,
            'message'   => $message
        ], $statusCode)));
    }
}
