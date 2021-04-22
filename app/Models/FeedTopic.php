<?php

namespace Core\Models;

use Illuminate\Database\Eloquent\Model;
use Core\FileStorage\FileMetaInterface;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Core\FileStorage\Traits\EloquentAttributeTrait;
use Plus\Feed\Models\Feed as FeedModel;

class FeedTopic extends Model
{
    use EloquentAttributeTrait;

    public const REVIEW_PASSED = 'passed';
    public const REVIEW_WAITING = 'waiting';
    public const REVIEW_FAILED = 'failed';

    /**
     * The model table name.
     */
    protected $table = 'feed_topics';

    /**
     * Parse resource and get resource meta.
     * @param null|string $resource
     * @return null|\Core\FileStorage\FileMetaInterface
     */
    protected function getLogoAttribute(?string $resource = null): ?FileMetaInterface
    {
        if (! $resource) {
            return null;
        }

        return $this->getFileStorageResourceMeta($resource);
    }

    /**
     * Set logo attribute.
     * @param mixed
     * @return self
     */
    protected function setLogoAttribute($resource)
    {
        $this->attributes['logo'] = (string) $resource;

        return $this;
    }

    /**
     * Topic belongs to many relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        $table = (new FeedTopicUserLink)->getTable();

        return $this
            ->belongsToMany(User::class, $table, 'topic_id', 'user_id')
            ->withPivot('index', Model::CREATED_AT, 'following_at')
            ->using(FeedTopicUserLink::class);
    }

    /**
     * The model feed velongs ti many relation.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function feeds(): BelongsToMany
    {
        $table = (new FeedTopicLink)->getTable();

        return $this
            ->belongsToMany(FeedModel::class, $table, 'topic_id', 'feed_id')
            ->using(FeedTopicLink::class);
    }

    /**
     * The topic creator has one relation.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'creator_user_id');
    }

    /**
     * The topic reports morph to many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
