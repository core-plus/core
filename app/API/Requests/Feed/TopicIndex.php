<?php

namespace Core\API\Requests\Feed;

use Core\API\Requests\Request;

class TopicIndex extends Request
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
            'index' => ['nullable', 'integer', 'min:0'],
            'direction' => ['nullable', 'in:asc,desc'],
            'q' => ['nullable', 'string'],
            'only' => ['nullable', 'string', 'in:hot'],
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
            'limit.max' => '请求数据量最多 100 条',
            'index.integer' => '请求参数类型非法，index 必须是整数',
            'index.min' => 'index 必须是大于 0 的正整数',
            'direction.in' => '排序方向值非法，必须是 `asc` 或者 `desc`',
            'q.string' => '输入的搜索关键词不合法',
            'only.in' => 'only 只接受 `hot` 值',
        ];
    }
}
