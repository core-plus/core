<?php

namespace Core\API\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Core\Api\Resources\UserCountsResource;
use Core\Models\UserCount as UserCountModel;

class UserCountsController extends Controller
{
    /**
     * Create the Controller instance.
     *
     * @author GEO <dev@kaifa.me>
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * The route controller to callable handle.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author GEO <dev@kaifa.me>
     */
    public function count(Request $request): JsonResponse
    {
        $counts = UserCountModel::query()->where('user_id', $request->user()->id)->get();
        $counts = $counts->keyBy('type')->map(function ($count) {
            return $count->total;
        });

        return (new UserCountsResource($counts->all()))
            ->response()
            ->setStatusCode(200);
    }

    /**
     * 重置某项为度数为0.
     * @Author   Wayne
     * @DateTime 2018-04-16
     * @Email    qiaobin@dev.com
     * @param    Request             $request [description]
     * @return   [type]                       [description]
     */
    public function reset(Request $request)
    {
        $type = $request->input('type');
        if ($type && in_array($type, ['commented', 'liked', 'system', 'group-post-pinned', 'post-comment-pinned', 'feed-comment-pinned', 'news-comment-pinned', 'post-pinned', 'mutual', 'following', 'group-join-pinned'])) {
            $type = 'user-'.$type;
        }

        UserCountModel::where('user_id', $request->user()->id)
            ->when($type, function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->update([
                'total' => 0,
                'read_at' => new Carbon(),
            ]);

        return response()->json('', 204);
    }
}
