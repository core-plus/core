<?php

namespace Core\Admin\Controllers\Setting;

use Illuminate\Http\Response;
use function Core\setting;
use Illuminate\Http\JsonResponse;
use Core\Admin\Controllers\Controller;
use Core\Admin\Requests\SetWeChatMpConfigure as SetWeChatMpConfigureRequest;

class WeChatMp extends Controller
{
    /**
     * Get configure.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfigure(): JsonResponse
    {
        $settings = setting('user', 'vendor:wechat-mp', [
            'appid' => '',
            'secret' => '',
        ]);

        return new JsonResponse($settings, Response::HTTP_OK);
    }

    /**
     * set configure.
     * @param \Core\Admin\Requests\SetWeChatMpConfigure $request
     * @return \Illuminate\Http\Response
     */
    public function setConfigure(SetWeChatMpConfigureRequest $request)
    {
        setting('user')->set('vendor:wechat-mp', [
            'appid' => $request->input('appid'),
            'secret' => $request->input('secret'),
        ]);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
