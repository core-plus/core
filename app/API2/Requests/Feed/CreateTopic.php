<?php

namespace Core\API2\Requests\Feed;

use Core\API2\Requests\Request;

class CreateTopic extends Request
{
    /**
     * Get the validator rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'desc' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'string', 'file_storage'],
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
            'name.required' => '请输入话题名称',
            'name.max' => '话题名称请控制在 100 字以内',
            'desc.max' => '话题描述请控制在 500 字以内',
            'logo.string' => '话题 Logo 数据非法',
            'logo.file_storage' => '话题 Logo 未上传，或使用非法节点',
        ];
    }
}
