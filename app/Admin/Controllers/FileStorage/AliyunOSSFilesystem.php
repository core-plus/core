<?php

namespace Core\Admin\Controllers\FileStorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function Core\setting;
use Illuminate\Http\JsonResponse;

class AliyunOSSFilesystem
{
    /**
     * Get Aliyun OSS filesystem.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(): JsonResponse
    {
        $configure = setting('file-storage', 'filesystems.aliyun-oss', []);
        $result = [
            'accessKeyId' => $configure['access-key-id'] ?? null,
            'accessKeySecret' => $configure['access-key-secret'] ?? null,
            'bucket' => $configure['bucket'] ?? null,
            'acl' => $configure['acl'] ?? 'public-read',
            'timeout' => $configure['timeout'] ?? 3600,
            'domain' => $configure['domain'] ?? null,
            'insideDomain' => $configure['inside-domain'] ?? null,
        ];

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Update Aliyun OSS filesystem.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): Response
    {
        $request->validate($this->rules(), $this->messages());
        $setting = setting('file-storage');
        $setting->set('filesystems.aliyun-oss', [
            'access-key-id' => $request->input('accessKeyId'),
            'access-key-secret' => $request->input('accessKeySecret'),
            'bucket' => $request->input('bucket'),
            'acl' => $request->input('acl'),
            'timeout' => $request->input('timeout'),
            'domain' => $request->input('domain'),
            'inside-domain' => $request->input('insideDomain'),
        ]);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    protected function rules(): array
    {
        return [
            'accessKeyId' => ['required', 'string'],
            'accessKeySecret' => ['required', 'string'],
            'bucket' => ['required', 'string'],
            'acl' => ['required', 'string', 'in:public-read-write,public-read,private'],
            'timeout' => ['required', 'integer'],
            'domain' => ['required', 'string', 'url'],
            'insideDomain' => ['required', 'string', 'url'],
        ];
    }

    protected function messages(): array
    {
        return [];
    }
}
