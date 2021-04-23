<?php

namespace Core\Notifications;

use Illuminate\Bus\Queueable;
use Core\Models\User as UserModel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Core\Models\Comment as CommentModel;
use Medz\Laravel\Notifications\JPush\Message as JPushMessage;

class Comment extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;
    protected $sender;

    /**
     * Create a new notification instance.
     *
     * @param CommentModel $comment
     * @param UserModel $sender
     */
    public function __construct(CommentModel $comment, UserModel $sender)
    {
        $this->comment = $comment;
        $this->sender = $sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($notifiable->id === $this->sender->id) {
            return [];
        }

        return ['database', 'jpush'];
    }

    /**
     * Get the JPush representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return JPushMessage
     */
    public function toJpush($notifiable): JPushMessage
    {
        $action = $notifiable->id === $this->comment->reply_user ? '回复' : '评论';
        $alert = sprintf('%s%s了你：%s', $this->sender->name, $action, $this->comment->body);
        $extras = [
            'tag' => 'notification:comments',
        ];

        $payload = new JPushMessage;
        $payload->setMessage($alert, [
            'content_type' => $extras['tag'],
            'extras' => $extras,
        ]);
        $payload->setNotification(JPushMessage::IOS, $alert, [
            'content-available' => false,
            'thread-id' => $extras['tag'],
            'extras' => $extras,
        ]);
        $payload->setNotification(JPushMessage::ANDROID, $alert, [
            'extras' => $extras,
        ]);
        $payload->setOptions([
            'apns_production' => boolval(config('services.jpush.apns_production')),
        ]);

        return $payload;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'contents' => $this->comment->body,
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
            ],
            'commentable' => [
                'type' => $this->comment->commentable_type,
                'id' => $this->comment->commentable_id,
            ],
            'hasReply' => $notifiable->id === $this->comment->reply_user ? true : false,
        ];
    }
}
