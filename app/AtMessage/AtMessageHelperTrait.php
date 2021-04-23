<?php

namespace Core\AtMessage;

use Core\Models\User as UserModel;

trait AtMessageHelperTrait
{
    /**
     * Send at message.
     * @param string $content
     * @param \Core\Models\User $sender
     * @param mixed $resource
     * @return void
     */
    public function sendAtMessage(string $content, UserModel $sender, $resource): void
    {
        preg_match_all('/\x{00ad}@((?:[^\/]+?))\x{00ad}/iu', $content, $matches);
        if (! is_array($matches[1]) || empty($matches[1])) {
            return;
        }

        $users = UserModel::where(function ($query) use ($matches) {
            $query = $query->where('name', 'like', array_pop($matches[1]));
            foreach ($matches[1] as $username) {
                $query = $query->where('name', 'like', $username);
            }

            return $query;
        })->get();
        $message = app(Message::class);

        foreach ($users as $user) {
            $message->send($sender, $user, $resource);
        }
    }
}
