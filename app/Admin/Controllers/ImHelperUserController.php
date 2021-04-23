<?php

namespace Core\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Core\Support\Configuration;
use Core\Admin\Requests\UpdateImHelperUserRequest;

class ImHelperUserController extends Controller
{
    /**
     * Fetch im helper user id.
     *
     * @return \Illuminate\Http\JsonResponse
     * @author GEO <dev@kaifa.me>
     */
    public function fetch(): JsonResponse
    {
        return response()->json(['user' => config('im.helper-user')], 200);
    }

    /**
     * Update im helper user id.
     *
     * @param \Core\Admin\Requests\UpdateImHelperUserRequest $request
     * @param \Core\Support\Configuration $config
     * @return \Illuminate\Http\Response
     * @author GEO <dev@kaifa.me>
     */
    // public function update(UpdateImHelperUserRequest $request, Configuration $config): Response
    public function update(Request $request, Configuration $config): Response
    {
        $config->set('im.helper-user', $request->input('user', ''));

        return response('', 204);
    }
}
