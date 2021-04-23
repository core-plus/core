<?php

namespace Core\API\Controllers\User\Message;

use Illuminate\Http\Response;
use Core\API\Controllers\Controller;
use Core\Models\AtMessage as AtMessageModel;
use Core\API\Requests\User\Message\ListAtMessageLine;
use Core\API\Resources\User\Message\AtMessage as AtMessageResource;

class At extends Controller
{
    /**
     * Create the at message controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function __invoke(ListAtMessageLine $request, AtMessageModel $model)
    {
        $direction = $request->query('direction', 'desc');
        $collection = $model
            ->query()
            ->when($index = $request->query('index'), function ($query) use ($index, $direction) {
                return $query->where('id', $direction === 'asc' ? '>' : '<', $index);
            })
            ->where('user_id', $request->user()->id)
            ->limit($request->query('limit', 15))
            ->orderBy('id', $direction)
            ->get();

        // 暂不提供支持，动态付费内容难以处理！代码请不要删除！
        // $relationships = array_filter(explode(',', $request->query('load')));
        // if ($relationships) {
        //     $collection->load($relationships);
        // }

        return AtMessageResource::collection($collection)
            ->toResponse($request)
            ->setStatusCode(Response::HTTP_OK);
    }
}
