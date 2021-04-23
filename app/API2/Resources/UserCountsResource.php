<?php

namespace Core\API2\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCountsResource extends JsonResource
{
    /**
     * The resource to array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function toArray($request): array
    {
        // unused the $request.
        unset($request);

        return [
            'user' => [
                'following' => $this['user-following'] ?? 0,
                'liked' => $this['user-liked'] ?? 0,
                'commented' => $this['user-commented'] ?? 0,
                'system' => $this['user-system'] ?? 0,
                'news-comment-pinned' => $this['user-news-comment-pinned'] ?? 0,
                'feed-comment-pinned' => $this['user-feed-comment-pinned'] ?? 0,
                'mutual' => $this['user-mutual'] ?? 0,
                'at' => $this['at'] ?? 0,
            ],
        ];
    }
}
