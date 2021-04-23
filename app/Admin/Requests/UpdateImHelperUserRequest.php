<?php

namespace Core\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImHelperUserRequest extends FormRequest
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
            'user' => 'required|integer|exists:users,id',
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
            'user.required' => '请输入助手用户 ID',
            'user.integer' => '输入的助手用户 ID 不合法，必须是整数类型',
            'user.exists' => '设置的助手用户不存在',
        ];
    }
}
