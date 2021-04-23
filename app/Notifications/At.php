<?php

namespace Core\Notifications;

use Illuminate\Bus\Queueable;
use Core\Models\User as UserModel;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Core\AtMessage\ResourceInterface;
use Medz\Laravel\Notifications\JPush\Message as JPushMessage;

class At extends Notification implements ShouldQueue
{
    use Queueable;

    protected $resource;
    protected $sender;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ResourceInterface $resource, UserModel $sender)
    {
        $this->resource = $resource;
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
     * @return \Medz\Laravel\Notifications\JPush\Message
     */
    public function toJpush($notifiable): JPushMessage
    {
        $alert = $this->resource->message();
        $extras = [
            'tag' => 'notification:at',
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
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
            ],
            'resource' => [
                'type' => $this->resource->type(),
                'id' => $this->resource->id(),
            ],
        ];
    }
}
