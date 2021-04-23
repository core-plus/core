<?php

namespace Core\API2\Controllers\Feed;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Core\API2\Controllers\Controller;
use Core\Models\FeedTopicLink as FeedTopicLinkModel;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Plus\Feed\Repository\Feed as FeedRepository;
use Core\API2\Requests\Feed\ListFeedsForATopic as ListFeedsForATopicRequest;

class TopicFeed extends Controller
{
    /**
     * The list feeds for a topic action handle.
     */
    public function __invoke(ListFeedsForATopicRequest $request, FeedTopicLinkModel $model, FeedRepository $repository, int $topic): JsonResponse
    {
        $userID = $request->user('api')->id ?? 0;
        $direction = $request->query('direction', 'desc');
        $links = $model
            ->query()
            ->where('topic_id', $topic)
            ->when((bool) ($index = $request->query('index', false)), function (EloquentBuilder $query) use ($index, $direction): EloquentBuilder {
                return $query->where('index', $direction === 'asc' ? '>' : '<', $index);
            })
            ->select('index', 'feed_id')
            ->orderBy('index', $direction)
            ->limit($request->query('limit', 15))
            ->get();
        $links->load([
            'feed',
            'feed.topics' => function ($query) {
                return $query->select('id', 'name');
            },
            'feed.user' => function ($query) {
                return $query
                    ->withTrashed()
                    ->with('certification');
            },
            'feed.pinnedComments' => function ($query) {
                return $query->with([
                    'user',
                    'user.certification',
                ])
                ->where('expires_at', '>', new Carbon)
                ->orderBy('amount', 'desc')
                ->orderBy('created_at', 'desc');
            },
        ]);

        $feeds = $links->map(function (FeedTopicLinkModel $link) use ($userID, $repository) {
            $feed = $link->feed;
            $feed->index = $link->index;

            $repository->setModel($feed);
            $repository->images();
            $repository->format($userID);
            $repository->previewComments();

            $feed->has_collect = $feed->collected($userID);
            $feed->has_like = $feed->liked($userID);

            return $feed;
        });

        return new JsonResponse($feeds, JsonResponse::HTTP_OK);
    }
}
