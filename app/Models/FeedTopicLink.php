<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Plus\Feed\Models\Feed as FeedModel;

class FeedTopicLink extends Pivot
{
    /**
     * The povot table name.
     */
    protected $table = 'feed_topic_links';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The pviot using primary key to index.
     */
    protected $primaryKey = 'index';

    /**
     * Load the link has feed relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function feed(): HasOne
    {
        return $this->hasOne(FeedModel::class, 'id', 'feed_id');
    }
}
