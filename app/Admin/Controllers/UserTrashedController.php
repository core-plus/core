<?php

namespace Core\Admin\Controllers;

use Illuminate\Http\Request;
use Core\Models\User as UserModel;
use Core\Admin\Resources\TrashedUserResource;

class UserTrashedController extends Controller
{
    /**
     * List trashed users.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 15);
        $offset = (int) $request->query('offset', 0);

        $users = UserModel::onlyTrashed()
            ->limit($limit)
            ->offset($offset)
            ->latest()
            ->get();

        return TrashedUserResource::collection($users)
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Restore a trashed user.
     *
     * @param int $user
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function restore(int $user)
    {
        $user = UserModel::withTrashed()
            ->where('id', $user)
            ->first();

        if ($user && $user->trashed()) {
            $user->restore();
        }

        return response('', 204);
    }
}
