<?php

namespace Core\API2\Requests\Feed;

use Core\API2\Requests\Request;

class ListParticipantsForATopic extends Request
{
    /**
     * Get the validator rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'offset' => ['nullable', 'integer', 'min:0'],
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
            'limit.integer' => '请求数据量必须是整数',
            'limit.min' => '请求数据量最少 1 条',
            'offset.integer' => '请求的数据偏移必须是整数',
            'offset.min' => '请求的数据偏移最少 0 条',
        ];
    }
}
