<?php

namespace Core\API2\Controllers\Feed;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Core\API2\Controllers\Controller;
use Core\Models\FeedTopicUserLink as FeedTopicUserLinkModel;
use Core\API2\Requests\Feed\ListParticipantsForATopic as ListParticipantsForATopicRequest;
use Core\API2\Resources\Feed\TopicParticipantCollection as TopicParticipantCollectionResponse;

class TopicParticipant extends Controller
{
    /**
     * List participants for a topic.
     *
     * @param \Core\API2\Requests\Feed\ListParticipantsForATopic $request
     * @param \Core\Models\FeedTopicUserLink $model
     * @param int $topic
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListParticipantsForATopicRequest $request, FeedTopicUserLinkModel $model, int $topic): JsonResponse
    {
        $result = $model
            ->query()
            ->where('topic_id', $topic)
            ->limit($request->query('limit', 15))
            ->offset($request->query('offset', 0))
            ->orderBy(Model::UPDATED_AT, 'desc')
            ->get();

        return (new TopicParticipantCollectionResponse($result))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_OK /* 200 */);
    }
}
