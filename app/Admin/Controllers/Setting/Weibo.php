<?php

namespace Core\Admin\Controllers\Setting;

use Illuminate\Http\Response;
use function Core\setting;
use Illuminate\Http\JsonResponse;
use Core\Admin\Controllers\Controller;
use Core\Admin\Requests\SetWeiboConfigure as SetWeiboConfigureRequest;

class Weibo extends Controller
{
    /**
     * Get configure.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfigure(): JsonResponse
    {
        $settings = setting('user', 'vendor:weibo', [
            'secret' => '',
            'appId' => '',
        ]);

        return new JsonResponse($settings, Response::HTTP_OK);
    }

    /**
     * set configure.
     * @param \Core\Admin\Requests\SetWeiboConfigureRequest $request
     * @return \Illuminate\Http\Response
     */
    public function setConfigure(SetWeiboConfigureRequest $request)
    {
        setting('user')->set('vendor:weibo', [
            'secret' => $request->input('secret'),
            'appId' => $request->input('appId'),
        ]);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
