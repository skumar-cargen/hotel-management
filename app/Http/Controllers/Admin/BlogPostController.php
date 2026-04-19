<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BlogPostStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendNewBlogPostNotification;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BlogPost::query()->with(['category', 'author', 'domains'])->latest();

            return DataTables::of($query)
                ->addColumn('category_name', function ($post) {
                    return $post->category
                        ? '<span class="badge bg-info">'.e($post->category->name).'</span>'
                        : '<span class="text-muted">—</span>';
                })
                ->addColumn('author_name', function ($post) {
                    return $post->author ? e($post->author->name) : '<span class="text-muted">—</span>';
                })
                ->addColumn('domain_list', function ($post) {
                    return $post->domains->pluck('name')->take(3)->map(fn ($n) => '<span class="badge bg-secondary me-1">'.e($n).'</span>')->implode('');
                })
                ->addColumn('status_label', function ($post) {
                    $labels = [
                        'draft' => ['Draft', 'warning'],
                        'published' => ['Published', 'success'],
                        'archived' => ['Archived', 'secondary'],
                    ];
                    $label = $labels[$post->status->value] ?? ['Unknown', 'secondary'];

                    return '<span class="badge bg-'.$label[1].'">'.$label[0].'</span>';
                })
                ->addColumn('published_date', function ($post) {
                    return $post->published_at
                        ? '<span style="font-size:.82rem;">'.$post->published_at->format('M j, Y').'</span>'
                        : '<span class="text-muted">—</span>';
                })
                ->addColumn('action', function ($post) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.blog-posts.show', $post).'"><i class="bx bx-show me-2"></i>Preview</a></li>
                            <li><a class="dropdown-item" href="'.route('admin.blog-posts.edit', $post).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.blog-posts.destroy', $post).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this blog post?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['category_name', 'author_name', 'domain_list', 'status_label', 'published_date', 'action'])
                ->make(true);
        }

        return view('admin.blog-posts.index');
    }

    public function create()
    {
        return view('admin.blog-posts.edit', [
            'post' => new BlogPost,
            'categories' => BlogCategory::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'seo_content' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'og_image' => 'nullable|image|max:2048',
            'domains' => 'nullable|array',
            'domains.*' => 'exists:domains,id',
        ]);

        // Slug with collision handling
        $slug = Str::slug($validated['title']);
        $count = BlogPost::withTrashed()->where('slug', $slug)->count();
        $validated['slug'] = $count ? "{$slug}-{$count}" : $slug;

        // Booleans
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        // Author
        $validated['user_id'] = auth()->id();

        // Tags: comma-separated string to JSON array
        $validated['tags'] = $this->parseTags($request->input('tags'));

        // Auto-set published_at
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        // OG image upload
        if ($request->hasFile('og_image')) {
            $validated['og_image'] = $request->file('og_image')->store('blog/og', 'public');
        }

        unset($validated['domains']);
        $post = BlogPost::create($validated);
        $post->domains()->sync($request->input('domains', []));

        if ($post->status === BlogPostStatus::Published) {
            SendNewBlogPostNotification::dispatch($post);
        }

        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post created successfully.');
    }

    public function show(BlogPost $blogPost)
    {
        $blogPost->load(['category', 'author', 'domains']);

        return view('admin.blog-posts.show', ['post' => $blogPost]);
    }

    public function edit(BlogPost $blogPost)
    {
        $blogPost->load('domains');

        return view('admin.blog-posts.edit', [
            'post' => $blogPost,
            'categories' => BlogCategory::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'seo_content' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'og_image' => 'nullable|image|max:2048',
            'domains' => 'nullable|array',
            'domains.*' => 'exists:domains,id',
        ]);

        // Slug with collision handling
        $slug = Str::slug($validated['title']);
        $count = BlogPost::withTrashed()->where('slug', $slug)->where('id', '!=', $blogPost->id)->count();
        $validated['slug'] = $count ? "{$slug}-{$count}" : $slug;

        // Booleans
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        // Tags
        $validated['tags'] = $this->parseTags($request->input('tags'));

        // Auto-set published_at
        if ($validated['status'] === 'published' && empty($validated['published_at']) && ! $blogPost->published_at) {
            $validated['published_at'] = now();
        }

        // Featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        // OG image upload
        if ($request->hasFile('og_image')) {
            $validated['og_image'] = $request->file('og_image')->store('blog/og', 'public');
        }

        $wasPublished = $blogPost->status === BlogPostStatus::Published;

        unset($validated['domains']);
        $blogPost->update($validated);
        $blogPost->domains()->sync($request->input('domains', []));

        if (!$wasPublished && $blogPost->status === BlogPostStatus::Published) {
            SendNewBlogPostNotification::dispatch($blogPost->fresh());
        }

        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post deleted successfully.');
    }

    private function parseTags(?string $tags): ?array
    {
        if (empty($tags)) {
            return null;
        }

        return array_values(array_filter(array_map('trim', explode(',', $tags))));
    }
}
