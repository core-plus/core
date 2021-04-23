<?php

namespace Core\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Core\Support\Configuration;

class AboutUsController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        return response()->json(['aboutUs' => config('site.aboutUs')], 200);
    }

    /**
     * @param Request       $request
     * @param Configuration $config
     * @return Response
     */
    public function store(Request $request, Configuration $config): Response
    {
//        dd($request->input('url'));
        $config->set('site.aboutUs.url', $request->input('url'));
        $config->set('site.aboutUs.content', $request->input('content'));

        return response('', 204);
    }
}
