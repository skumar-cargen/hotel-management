<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = BlogCategory::query()->withCount('posts');

            return DataTables::of($query)
                ->addColumn('posts_count_label', function ($category) {
                    return '<span class="badge bg-primary">'.$category->posts_count.'</span>';
                })
                ->addColumn('status', function ($category) {
                    return $category->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($category) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.blog-categories.edit', $category).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.blog-categories.destroy', $category).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this category?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['posts_count_label', 'status', 'action'])
                ->make(true);
        }

        return view('admin.blog-categories.index');
    }

    public function create()
    {
        return view('admin.blog-categories.edit', ['category' => new BlogCategory]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $slug = Str::slug($validated['name']);
        $count = BlogCategory::withTrashed()->where('slug', $slug)->count();
        $validated['slug'] = $count ? "{$slug}-{$count}" : $slug;
        $validated['is_active'] = $request->boolean('is_active', true);

        BlogCategory::create($validated);

        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category created successfully.');
    }

    public function edit(BlogCategory $blogCategory)
    {
        return view('admin.blog-categories.edit', ['category' => $blogCategory]);
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $slug = Str::slug($validated['name']);
        $count = BlogCategory::withTrashed()->where('slug', $slug)->where('id', '!=', $blogCategory->id)->count();
        $validated['slug'] = $count ? "{$slug}-{$count}" : $slug;
        $validated['is_active'] = $request->boolean('is_active');

        $blogCategory->update($validated);

        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category updated successfully.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();

        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category deleted successfully.');
    }
}
