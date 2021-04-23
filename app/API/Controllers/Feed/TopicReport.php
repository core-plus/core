<?php

namespace Core\API\Controllers\Feed;

use Illuminate\Http\Response;
use Core\API\Controllers\Controller;
use Core\Models\Report as ReportModel;
use Core\Models\FeedTopic as FeedTopicModel;
use Core\API\Requests\Feed\ReportATopic as ReportATopicRequest;

class TopicReport extends Controller
{
    /**
     * Create the action instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Report a topic.
     *
     * @param \Core\API\Requests\Feed\ReportATopic $request
     * @param \Core\Models\FeedTopic $topic
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ReportATopicRequest $request, FeedTopicModel $topic): Response
    {
        $report = new ReportModel();
        $report->reason = $request->input('message');
        $report->user_id = $request->user()->id;
        $report->target_user = $topic->creator_user_id;
        $report->subject = sprintf('动态话题（%d）：%s', $topic->id, $topic->name);
        $report->status = 0;
        $topic->reports()->save($report);

        return (new Response)->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
