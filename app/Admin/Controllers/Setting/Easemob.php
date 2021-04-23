<?php

namespace Core\Admin\Controllers\Setting;

use Illuminate\Http\Response;
use function Core\setting;
use Illuminate\Http\JsonResponse;
use Core\Admin\Controllers\Controller;
use Core\Admin\Requests\SetEasemob as SetEasemobRequest;

class Easemob extends Controller
{
    /**
     * Get configure.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfigure(): JsonResponse
    {
        $settings = setting('user', 'vendor:easemob', [
            'open' => false,
            'appKey' => '',
            'clientId' => '',
            'clientSecret' => '',
            'registerType' => 0,
        ]);

        return new JsonResponse($settings, Response::HTTP_OK);
    }

    /**
     * set configure.
     * @param \Core\Admin\Requests\SetEasemob $request
     * @return \Illuminate\Http\Response
     */
    public function setConfigure(SetEasemobRequest $request)
    {
        setting('user')->set('vendor:easemob', [
            'open' => (bool) $request->input('open'),
            'appKey' => $request->input('appKey'),
            'clientId' => $request->input('clientId'),
            'clientSecret' => $request->input('clientSecret'),
            'registerType' => (int) $request->input('registerType'),
        ]);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
