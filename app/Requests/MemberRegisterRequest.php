<?php
namespace App\Requests;

class MemberRegisterRequest extends BaseFormRequest
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
            'name'              => 'string|max:20',
            'password'          => 'required|string|min:6',
            'email'             => 'required|email|max:255|unique:members',
        ];
    }

    public function messages()
    {
        return [
            'name.string'                => trans('名稱格式有誤'),
            'name.max'                   => trans('名稱最多20個字元'),
            'password.required'          => trans('密碼必填'),
            'password.string'            => trans('密碼格式有誤'),
            'password.min'               => trans('密碼最少6個字元'),
            'email.required'             => trans('Email已被註冊'),
            'email.email'                => trans('Email格式有誤'),
            'email.max'                  => trans('Email最多255個字元'),
        ];
    }
}
