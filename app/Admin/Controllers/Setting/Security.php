<?php

namespace Core\Admin\Controllers\Setting;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function Core\setting;
use Illuminate\Http\JsonResponse;
use Core\Admin\Controllers\Controller;

class Security extends Controller
{
    /**
     * Get pay validate password switch.
     * @return \Illuminate\Http\JsonResponse
     */
    public function payValidateSwitch(): JsonResponse
    {
        $switch = (bool) setting('pay', 'validate-password', false);

        return new JsonResponse(['switch' => $switch], JsonResponse::HTTP_OK);
    }

    /**
     * Change pay validate password switch.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePayValidateSwitch(Request $request): Response
    {
        $switch = (bool) $request->input('switch', false);
        setting('pay')->set('validate-password', $switch);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
