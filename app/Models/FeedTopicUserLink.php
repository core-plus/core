<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FeedTopicUserLink extends Pivot
{
    /**
     * The model table name.
     */
    protected $table = 'feed_topic_user_links';

    /**
     * The pviot using primary key to index.
     */
    protected $primaryKey = 'index';
}
