<?php

namespace Core\API2\Requests\Feed;

use Core\API2\Requests\Request;

class ReportATopic extends Request
{
    /**
     * Get the validator rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get the validator error messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'message.required' => '请输入举报理由',
            'message.max' => '举报理由必须在 255 个字以内',
        ];
    }
}
