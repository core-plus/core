<?php

namespace Core\API\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Core\Utils\DateTimeToIso8601ZuluString;
use Core\Notifications\At as AtNotification;
use Core\Notifications\Like as LikeNotification;
use Core\Notifications\Follow as FollowNotification;
use Core\Notifications\System as SystemNotification;
use Core\Notifications\Comment as CommentNotification;
use Core\API\Resources\Notification as NotificationResource;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class NotificationController extends Controller
{
    use DateTimeToIso8601ZuluString;

    /**
     * Get the notification controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get the request notification type.
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function getQueryType(Request $request, bool $getTypes = false)
    {
        $type = $request->input('type');
        $types = [
            'at' => AtNotification::class,
            'comment' => CommentNotification::class,
            'like' => LikeNotification::class,
            'system' => SystemNotification::class,
        ];

        if ($getTypes) {
            return $types;
        } elseif (array_key_exists($type, $types)) {
            return $types[$type];
        }

        throw new UnprocessableEntityHttpException('type 不合法');
    }

    /**
     * Get the user notifications.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->whereType($this->getQueryType($request))
            ->paginate(15)
            ->appends([
                'type' => $request->query('type'),
            ]);

        return NotificationResource::collection($notifications);
    }

    /**
     * Set notifications make read at.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $type = $this->getQueryType($request);
        $notifications = $request->user()
            ->unreadNotifications()
            ->whereType($type)
            ->update([
                'read_at' => now(),
            ]);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Clear follow notifications.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function clearFollowNotifications(Request $request)
    {
        $request->user()->notifications()->whereType(FollowNotification::class)->delete();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Get the user notification statistics.
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function statistics(Request $request)
    {
        $statistics = [];
        foreach ($this->getQueryType($request, true) as $alias => $notificationClassname) {
            $badge = $request->user()->unreadNotifications()->whereType($notificationClassname)->count();
            if ($notificationClassname === SystemNotification::class) {
                $first = $request->user()->notifications(SystemNotification::class)->first();
                $statistics[$alias] = [
                    'badge' => $badge,
                ];
                $statistics[$alias] = array_merge($statistics[$alias], (! $first) ? [] : [
                    'first' => new NotificationResource($first),
                ]);
                continue;
            }

            $lastCreatedAt = $this->dateTimeToIso8601ZuluString(
                $request->user()->notifications()->whereType($notificationClassname)->first()->created_at ?? null
            );
            $previewUserNames = $request->user()
                ->notifications()
                ->whereType($notificationClassname)
                ->limit(5)
                ->get()
                ->map(function ($notification) {
                    return $notification->data['sender']['name'];
                })
                ->filter()
                ->unique()
                ->values()
                ->all();
            $statistics[$alias] = [
                'badge' => $badge,
                'last_created_at' => $lastCreatedAt,
                'preview_users_names' => $previewUserNames,
            ];
        }

        $statistics['follow'] = [
            'badge' => $request->user()->unreadNotifications()->whereType(FollowNotification::class)->count(),
        ];

        return new JsonResponse($statistics);
    }
}
