<?php

namespace Core\EaseMobIM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckGroup extends FormRequest
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
            'groupname' => 'required|string',
            'desc' => 'required|string',
            'numbers' => 'string',
            'public' => 'boolean|nullable',
            'members_only' => 'nullable',
            'allowinvites' => 'boolean|nullable',
        ];
    }

    /**
     * return validation messages.
     */
    public function messages()
    {
        return [
            'groupname.required' => '群组名称不能为空',
            'desc.required' => '群组简介不能为空',
        ];
    }
}
