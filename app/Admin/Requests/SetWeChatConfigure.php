<?php

namespace Core\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetWeChatConfigure extends FormRequest
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
            'appSecret' => 'required|string',
            'appKey' => 'required|string',
        ];
    }
}
