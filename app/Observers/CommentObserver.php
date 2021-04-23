<?php

namespace Core\Observers;

use Core\Models\Comment;
use Illuminate\Support\Facades\Cache;

class CommentObserver
{
    public function creating(Comment $comment)
    {
        // 设置重复内容锁
        Cache::put('comment_mark_'.$comment->comment_mark, $comment->comment_mark, 3);
        unset($comment->comment_mark);
    }
}
