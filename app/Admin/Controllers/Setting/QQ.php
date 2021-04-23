<?php

namespace Core\Admin\Controllers\Setting;

use Illuminate\Http\Response;
use function Core\setting;
use Illuminate\Http\JsonResponse;
use Core\Admin\Controllers\Controller;
use Core\Admin\Requests\SetQQConfigure as SetQQConfigureRequest;

class QQ extends Controller
{
    /**
     * Get configure.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfigure(): JsonResponse
    {
        $settings = setting('user', 'vendor:qq', [
            'appId' => '',
            'appKey' => '',
        ]);

        return new JsonResponse($settings, Response::HTTP_OK);
    }

    /**
     * set configure.
     * @param \Core\Admin\Requests\SetQQConfigure $request
     * @return \Illuminate\Http\Response
     */
    public function setConfigure(SetQQConfigureRequest $request)
    {
        setting('user')->set('vendor:qq', [
            'appId' => $request->input('appId'),
            'appKey' => $request->input('appKey'),
        ]);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
