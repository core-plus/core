<?php

namespace Core\Admin\Controllers\Setting;

use Illuminate\Http\Response;
use function Core\setting;
use Illuminate\Http\JsonResponse;
use Core\Admin\Controllers\Controller;
use Core\Admin\Requests\SetWeChatConfigure as SetWeChatConfigureRequest;

class WeChat extends Controller
{
    /**
     * Get configure.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfigure(): JsonResponse
    {
        $settings = setting('user', 'vendor:wechat', [
            'appSecret' => '',
            'appKey' => '',
        ]);

        return new JsonResponse($settings, Response::HTTP_OK);
    }

    /**
     * set configure.
     * @param \Core\Admin\Requests\SetWeChatConfigureRequest $request
     * @return \Illuminate\Http\Response
     */
    public function setConfigure(SetWeChatConfigureRequest $request)
    {
        setting('user')->set('vendor:wechat', [
            'appSecret' => $request->input('appSecret'),
            'appKey' => $request->input('appKey'),
        ]);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
