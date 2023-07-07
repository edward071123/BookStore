<?php
namespace App\Requests;

class BookRequest extends BaseFormRequest
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
            'title'           => 'required|string|max:30',
            'author'          => 'required|string|max:30',
            'publicationDate' => 'required|date_format:Y-m-d',
            'category'        => 'required|string|max:30',
            'price'           => 'nullable|integer',
            'quantity'        => 'nullable|integer'
        ];
    }

    public function messages()
    {
        return [
            'title.required'                => trans('title必填'),
            'title.string'                  => trans('title格式有誤'),
            'title.max'                     => trans('title最多30個字元'),
            'author.required'               => trans('author必填'),
            'author.string'                 => trans('author格式有誤'),
            'author.max'                    => trans('author最多30個字元'),
            'category.required'             => trans('category必填'),
            'category.string'               => trans('category格式有誤'),
            'category.max'                  => trans('category最多30個字元'),
            'publicationDate.required'      => trans('publicationDate必填'),
            'publicationDate.date_format'   => trans('publicationDate格式有誤'),
        ];
    }
}
