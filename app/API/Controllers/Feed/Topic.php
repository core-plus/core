<?php

namespace Core\API\Controllers\Feed;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use function Core\setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;
use Core\API\Controllers\Controller;
use Core\Models\FeedTopic as FeedTopicModel;
use Core\API\Resources\Feed\Topic as TopicResource;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Core\API\Requests\Feed\TopicIndex as IndexRequest;
use Core\API\Requests\Feed\EditTopic as EditTopicRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Core\Models\FeedTopicUserLink as FeedTopicUserLinkModel;
use Core\API\Requests\Feed\CreateTopic as CreateTopicRequest;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Topic extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        // Add Auth(api) middleware.
        $this
            ->middleware('auth:api')
            ->only(['create', 'update']);

        // Add DisposeSensitive middleware.
        $this
            ->middleware('sensitive:name,desc')
            ->only(['create', 'update']);
    }

    public function listTopicsOnlyHot(Request $request, FeedTopicModel $model)
    : JsonResponse
    {
        $user = $request->user('api');
        $topics = $model
            ->query()
            ->whereNotNull('hot_at')
            ->where('status', FeedTopicModel::REVIEW_PASSED)
            ->limit(8)
            ->orderBy('id', 'desc')
            ->get();
        if ($user) {
            $topics->load([
                'users' => function ($query) use ($user) {
                    return $query->wherePivot('user_id', $user->id);
                },
            ]);
        }

        return TopicResource::collection($topics)
            ->response()
            ->setStatusCode(Response::HTTP_OK /* 200 */);
    }

    /**
     * List topics.
     *
     * @param  \Core\Requests\Feed\TopicIndex  $request
     * @param  \Core\Models\FeedTopic  $model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request, FeedTopicModel $model)
    : JsonResponse
    {
        if ($request->query('only') === 'hot') {
            return $this->listTopicsOnlyHot($request, $model);
        }
        $user = $request->user('api');

        // Get query data `id` order direction.
        // Value: `asc` or `desc`
        $direction = $request->query('direction', 'desc');

        // Query database data.
        $result = $model
            ->query()
            ->where('status', FeedTopicModel::REVIEW_PASSED)
            // If `$request->query('q')` param exists,
            // create "`name` like %?%" SQL where.
            ->when((bool) ($searchKeyword = $request->query('q', false)),
                function (EloquentBuilder $query) use ($searchKeyword) {
                    return $query->where('name', 'like',
                        sprintf('%%%s%%', $searchKeyword));
                })
            // If `$request->query('index)` param exists,
            // using `$direction` create "id ? ?" where
            //     ?[0] `$direction === asc` is `>`
            //     ?[0] `$direction === desc` is `<`
            ->when((bool) ($indexID = $request->query('index', false)),
                function (EloquentBuilder $query) use ($indexID, $direction) {
                    return $query->where('id', $direction === 'asc' ? '>' : '<',
                        $indexID);
                })
            // Set the number of data
            ->limit($request->query('limit', 15))
            // Using `$direction` set `id` direction,
            // the `$direction` enum `asc` or `desc`.
            ->orderBy('id', $direction)
            // Run the SQL query, return a collection.
            // instanceof \Illuminate\Support\Collection
            ->get();
        if ($user) {
            $result->load([
                'users' => function ($query) use ($user) {
                    return $query->wherePivot('user_id', $user->id);
                },
            ]);
        }

        // Create the action response.
        $response = TopicResource::collection($result)
            ->response()
            ->setStatusCode(Response::HTTP_OK /* 200 */);

        return $response;
    }

    /**
     * Create an topic.
     *
     * @param  \Core\API\Requests\Feed\CreateTopic  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateTopicRequest $request)
    : JsonResponse
    {
        // Create feed topic module
        $topic = new FeedTopicModel;
        foreach ($request->only(['name', 'logo', 'desc']) as $key => $value) {
            $topic->{$key} = $value;
        }

        // Database query `name` used
        $exists = $topic
            ->query()
            ->where('name', $topic->name)
            ->exists();
        if ($exists) {
            throw new UnprocessableEntityHttpException(sprintf('???%s??????????????????',
                $topic->name));
        }

        // Fetch the authentication user model.
        $user = $request->user();

        // Open a database transaction,
        // database commit success return the topic model.
        $topic = $user->getConnection()->transaction(function () use (
            $user,
            $topic
        ) {
            // Set topic creator user ID and
            // init default followers count.
            $topic->creator_user_id = $user->id;
            $topic->followers_count = 1;
            $topic->status = setting('feed', 'topic:need-review', false)
                ? FeedTopicModel::REVIEW_WAITING
                : FeedTopicModel::REVIEW_PASSED;
            $topic->save();

            // Attach the creator user follow the topic.
            $link = new FeedTopicUserLinkModel();
            $link->topic_id = $topic->id;
            $link->user_id = $user->id;
            $link->following_at = new Carbon();
            $link->save();

            return $topic;
        });

        // Headers:
        //      Status: 201 Created
        // Body:
        //      { "id": $topid->id }
        return new JsonResponse(
            [
                'id' => $topic->id,
                'need_review' => setting('feed', 'topic:need-review', false),
            ],
            Response::HTTP_CREATED /* 201 */
        );
    }

    /**
     * Edit an topic.
     *
     * @param  \Core\API\Requests\Feed\EditTopic  $request
     * @param  \Core\Models\FeedTopic  $topic
     *
     * @return \Illuminate\Http\Response
     */
    public function update(EditTopicRequest $request, FeedTopicModel $topic)
    : Response
    {
        $this->authorize('update', $topic);

        // Create success 204 response
        $response
            = (new Response())->setStatusCode(Response::HTTP_NO_CONTENT /* 204 */);

        // If `logo` and `desc` field all is NULL
        $data = array_filter($request->only(['name', 'desc', 'logo']));
        if (empty($data)) {
            return $response;
        }

        foreach ($data as $key => $value) {
            $topic->{$key} = $value;
        }
        $topic->save();

        return $response;
    }

    /**
     * Get a single topic.
     *
     * @param  \Core\Models\FeedTopic  $topic
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(FeedTopicModel $topic)
    : JsonResponse
    {
        if ($topic->status !== FeedTopicModel::REVIEW_PASSED) {
            throw new NotFoundHttpException('??????????????????????????????????????????');
        }

        $topic->participants = $topic
            ->users()
            ->newPivotStatement()
            ->where('topic_id', $topic->id)
            ->where('user_id', '!=', $topic->creator_user_id)
            ->orderBy(Model::UPDATED_AT, 'desc')
            ->limit(3)
            ->select('user_id')
            ->get()
            ->pluck('user_id');

        return (new TopicResource($topic))
            ->response()
            ->setStatusCode(Response::HTTP_OK /* 200 */);
    }
}
