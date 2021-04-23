<?php

namespace Core\API2\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Plus\News\Models\News;

class NewsPostController extends Controller
{
    /**
     * Create the news posts controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Destory a News post.
     * @param \Plus\News\Models\News $post
     * @return mixed
     */
    public function destroy(News $post)
    {
        $this->authorize('delete', $post);

        // Database transaction
        DB::transaction(function () use ($post) {
            $post->pinned()->delete();
            $post->applylog()->delete();
            $post->reports()->delete();
            $post->tags()->detach();
            $post->delete();
        });

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
