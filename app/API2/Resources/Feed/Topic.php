<?php

namespace Core\API2\Resources\Feed;

use Illuminate\Http\Resources\Json\JsonResource;

class Topic extends JsonResource
{
    /**
     * The topic resource to array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $user = $request->user('api');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => $this->when($this->logo, function () {
                return $this->logo->toArray();
            }),
            'desc' => $this->when($this->desc, $this->desc),
            'creator_user_id' => $this->creator_user_id,
            'feeds_count' => $this->feeds_count,
            'followers_count' => $this->followers_count,
            'has_followed' => $this->when($user, function () use ($user) {
                $link = $this->users->firstWhere('id', $user->id)->pivot ?? null;
                if ($link && $link->following_at) {
                    return true;
                }

                return false;
            }),
            'participants' => $this->when($this->participants, $this->participants),
        ];
    }
}
