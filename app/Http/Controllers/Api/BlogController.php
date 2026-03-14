<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogCategoryResource;
use App\Http\Resources\BlogPostDetailResource;
use App\Http\Resources\BlogPostListResource;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use ApiResponses;

    public function categories()
    {
        $domain = $this->domain();

        $categories = BlogCategory::active()
            ->withCount(['posts' => fn ($q) => $q->published()
                ->whereHas('domains', fn ($d) => $d->where('domains.id', $domain->id)),
            ])
            ->having('posts_count', '>', 0)
            ->orderBy('name')
            ->get();

        return $this->successResponse(BlogCategoryResource::collection($categories));
    }

    public function index(Request $request)
    {
        $domain = $this->domain();

        $query = BlogPost::published()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id))
            ->with(['category', 'author']);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%"));
        }

        $posts = $query->orderByDesc('published_at')
            ->paginate($request->integer('per_page', 12));

        return $this->paginatedResponse(BlogPostListResource::collection($posts));
    }

    public function featured()
    {
        $domain = $this->domain();

        $posts = BlogPost::published()
            ->featured()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id))
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->get();

        return $this->successResponse(BlogPostListResource::collection($posts));
    }

    public function show(string $slug)
    {
        $domain = $this->domain();

        $post = BlogPost::published()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id))
            ->where('slug', $slug)
            ->with(['category', 'author'])
            ->first();

        if (! $post) {
            return $this->errorResponse('Blog post not found.', 404);
        }

        $post->increment('view_count');

        return $this->successResponse(new BlogPostDetailResource($post));
    }

    public function categoryPosts(Request $request, string $slug)
    {
        $domain = $this->domain();

        $category = BlogCategory::active()->where('slug', $slug)->first();

        if (! $category) {
            return $this->errorResponse('Category not found.', 404);
        }

        $posts = BlogPost::published()
            ->whereHas('domains', fn ($q) => $q->where('domains.id', $domain->id))
            ->where('blog_category_id', $category->id)
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->paginate($request->integer('per_page', 12));

        return $this->paginatedResponse(BlogPostListResource::collection($posts));
    }
}
