<?php

namespace Core\API2\Resources\Feed;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TopicParticipantCollection extends ResourceCollection
{
    /**
     * The collection to array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this
            ->collection
            ->map(function ($item) use ($request) {
                return $this->renderItem($item, $request);
            })
            ->all();
    }

    /**
     * Render the collection item.
     *
     * @param mixed $item
     * @return int
     */
    public function renderItem($item): int
    {
        return $item->user_id;
    }
}
