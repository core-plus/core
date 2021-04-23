<?php

namespace Core\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use function Core\findMarkdownImageIDs;
use Core\Models\FileWith as FileWithModel;

trait FindMarkdownFileTrait
{
    /**
     * Find markdown image IDs.
     *
     * @param string $markdown
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function findMarkdownImageIDs(string $markdown): array
    {
        return findMarkdownImageIDs($markdown);
    }

    /**
     * Find markdown images of collection.
     *
     * @param string $markdown
     * @param callable $call
     * @return \Illuminate\Support\Collection
     * @author GEO <dev@kaifa.me>
     */
    public function findMarkdownImageModels(string $markdown, $call = null): Collection
    {
        $IDs = $this->findMarkdownImageIDs($markdown);
        if (empty($IDs)) {
            return new Collection();
        }

        $query = FileWithModel::whereIn('id', $IDs);
        if ($call && is_callable($call)) {
            call_user_func($call, $query);
        }

        return $query->get();
    }

    /**
     * Find markdown images of collection for not with.
     *
     * @param string $markdown
     * @return \Illuminate\Support\Collection
     * @author GEO <dev@kaifa.me>
     */
    public function findMarkdownImageNotWithModels(string $markdown): Collection
    {
        return $this->findMarkdownImageModels($markdown, function (Builder $query) {
            $query->where('channel', null)
                ->where('raw', null);
        });
    }
}
