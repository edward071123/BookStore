<?php
namespace App\Requests;

class MemberAuthRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'         => 'required|email|max:255',
            'password'      => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.required'      => trans('Email必填'),
            'email.email'         => trans('Email格式有誤'),
            'email.max'           => trans('Email最多255個字元'),
            'password.required'   => trans('密碼必填'),
            'password.string'     => trans('密碼格式有誤')
        ];
    }
}
