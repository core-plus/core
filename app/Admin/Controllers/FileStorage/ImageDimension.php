<?php

namespace Core\Admin\Controllers\FileStorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function Core\setting;
use Illuminate\Http\JsonResponse;

class ImageDimension
{
    /**
     * Show file storage validate image dimension.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(): JsonResponse
    {
        // Get configure.
        $configure = setting('file-storage', 'task-create-validate', [
            'image-min-width' => 0,
            'image-max-width' => 0,
            'image-min-height' => 0,
            'image-max-height' => 0,
        ]);

        // Packing json result.
        $result = [
            'width' => [
                'min' => intval($configure['image-min-width'] ?? 0),
                'max' => intval($configure['image-max-width'] ?? 0),
            ],
            'height' => [
                'min' => intval($configure['image-min-height'] ?? 0),
                'max' => intval($configure['image-max-height'] ?? 0),
            ],
        ];

        return new JsonResponse($result, JsonResponse::HTTP_OK);
    }

    /**
     * Update file storage validate image dimension.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): Response
    {
        // Get configure.
        $setting = setting('file-storage');
        $setting->set('task-create-validate', array_merge((array) $setting->get('task-create-validate', []), array_filter([
            'image-min-width' => (int) $request->input('width.min', 0),
            'image-max-width' => (int) $request->input('width.max', 0),
            'image-min-height' => (int) $request->input('height.min', 0),
            'image-max-height' => (int) $request->input('height.max', 0),
        ])));

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
