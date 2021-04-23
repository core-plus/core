<?php

namespace Core\API\Requests\Feed;

use Core\API\Requests\Request;

class EditTopic extends Request
{
    /**
     * Get the validator rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'logo' => ['nullable', 'string', 'file_storage'],
            'desc' => ['nullable', 'string', 'max:500'],
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
            'desc.max' => '话题描述请控制在 500 字以内',
            'logo.string' => '话题 Logo 数据非法',
            'logo.file_storage' => '话题 Logo 未上传，或使用非法节点',
        ];
    }
}
