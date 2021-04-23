<?php

namespace Core\Notifications;

use Illuminate\Bus\Queueable;
use Overtrue\EasySms\Support\Config;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Config\Repository as ConfigRepository;
use Core\Models\VerificationCode as VerificationCodeModel;

class VerificationCode extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The notification verification code model.
     *
     * @var \Core\Models\VerificationCode
     */
    protected $model;

    /**
     * Create the verification notification instance.
     *
     * @param \Core\Models\VerificationCode $model
     * @author GEO <dev@kaifa.me>
     */
    public function __construct(VerificationCodeModel $model)
    {
        $this->model = $model;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param \Core\Models\VerificationCode $notifiable
     * @return array
     */
    public function via(VerificationCodeModel $notifiable)
    {
        return [$notifiable->channel];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param \Core\Models\VerificationCode $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(VerificationCodeModel $notifiable)
    {
        return (new MailMessage)->markdown('mails.varification_code', [
            'model' => $notifiable,
            'user' => $notifiable->user,
        ]);
    }

    /**
     * Get the SMS representation of the norification.
     *
     * @param \Core\Models\VerificationCode $notifiable
     * @return [type]
     * @author GEO <dev@kaifa.me>
     */
    public function toSms(VerificationCodeModel $notifiable, Config $config)
    {
        return new Messages\VerificationCodeMessage(
            new ConfigRepository($config->get('channels.code')),
            (int) $notifiable->code
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(): array
    {
        return $this->model->toArray();
    }
}
