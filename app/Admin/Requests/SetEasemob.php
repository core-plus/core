<?php

namespace Core\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetEasemob extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * @author GEO <dev@kaifa.me>
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function rules(): array
    {
        return [
            'open' => 'required|boolean',
            'appKey' => 'required|string|regex:/[a-z0-9-_]+#[a-z0-9-_]+/is',
            'clientId' => 'required|string',
            'clientSecret' => 'required|string',
            'registerType' => 'required|integer|in:0,1',
        ];
    }

    /**
     * Get the validation message that apply to the request.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function messages(): array
    {
        return [
            'appKey.regex' => ':attribute格式错误，请填入使用 `#` 符号分割的正确值',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'open' => '开关',
            'appKey' => 'App Key',
            'clientId' => 'Client ID',
            'clientSecret' => 'client Secret',
            'registerType' => '注册方式',
        ];
    }
}
