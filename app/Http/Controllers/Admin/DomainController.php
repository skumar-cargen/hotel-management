<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\DomainHeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Domain::query()->withCount('hotels');

            return DataTables::of($query)
                ->addColumn('status', function ($domain) {
                    return $domain->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('action', function ($domain) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.domains.edit', $domain).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><a class="dropdown-item" href="'.route('admin.domains.hero-slides.index', $domain).'"><i class="bx bx-slideshow me-2"></i>Domain Slides</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.domains.destroy', $domain).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this item?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.domains.index');
    }

    public function create()
    {
        return view('admin.domains.edit', ['domain' => new Domain]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:domains',
            'default_language' => 'required|string|max:5',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,svg,jpg,jpeg,webp|max:512',
            'about_us' => 'nullable|string',
            'about_us_meta_title' => 'nullable|string|max:255',
            'about_us_meta_description' => 'nullable|string|max:500',
            'about_us_canonical_url' => 'nullable|url|max:255',
            'privacy_policy' => 'nullable|string',
            'privacy_policy_meta_title' => 'nullable|string|max:255',
            'privacy_policy_meta_description' => 'nullable|string|max:500',
            'privacy_policy_canonical_url' => 'nullable|url|max:255',
            'terms_conditions' => 'nullable|string',
            'terms_conditions_meta_title' => 'nullable|string|max:255',
            'terms_conditions_meta_description' => 'nullable|string|max:500',
            'terms_conditions_canonical_url' => 'nullable|url|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('domains/logos', 'public');
        }
        unset($validated['logo']);

        if ($request->hasFile('favicon')) {
            $validated['favicon_path'] = $request->file('favicon')->store('domains/favicons', 'public');
        }
        unset($validated['favicon']);

        $domain = Domain::create($validated);

        return redirect()->route('admin.domains.edit', $domain)->with('success', 'Domain created successfully. You can now add hero slides.');
    }

    public function show(Domain $domain)
    {
        $domain->loadCount(['hotels', 'locations', 'bookings']);

        return view('admin.domains.show', compact('domain'));
    }

    public function edit(Domain $domain)
    {
        return view('admin.domains.edit', compact('domain'));
    }

    public function heroSlides(Domain $domain)
    {
        $domain->load('heroSlides');

        return view('admin.domains.hero-slides', compact('domain'));
    }

    public function update(Request $request, Domain $domain)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:domains,domain,'.$domain->id,
            'default_language' => 'required|string|max:5',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,svg,jpg,jpeg,webp|max:512',
            'about_us' => 'nullable|string',
            'about_us_meta_title' => 'nullable|string|max:255',
            'about_us_meta_description' => 'nullable|string|max:500',
            'about_us_canonical_url' => 'nullable|url|max:255',
            'privacy_policy' => 'nullable|string',
            'privacy_policy_meta_title' => 'nullable|string|max:255',
            'privacy_policy_meta_description' => 'nullable|string|max:500',
            'privacy_policy_canonical_url' => 'nullable|url|max:255',
            'terms_conditions' => 'nullable|string',
            'terms_conditions_meta_title' => 'nullable|string|max:255',
            'terms_conditions_meta_description' => 'nullable|string|max:500',
            'terms_conditions_canonical_url' => 'nullable|url|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            if ($domain->logo_path) {
                Storage::disk('public')->delete($domain->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('domains/logos', 'public');
        }
        unset($validated['logo']);

        if ($request->boolean('remove_logo') && $domain->logo_path) {
            Storage::disk('public')->delete($domain->logo_path);
            $validated['logo_path'] = null;
        }

        if ($request->hasFile('favicon')) {
            if ($domain->favicon_path) {
                Storage::disk('public')->delete($domain->favicon_path);
            }
            $validated['favicon_path'] = $request->file('favicon')->store('domains/favicons', 'public');
        }
        unset($validated['favicon']);

        if ($request->boolean('remove_favicon') && $domain->favicon_path) {
            Storage::disk('public')->delete($domain->favicon_path);
            $validated['favicon_path'] = null;
        }

        $domain->update($validated);

        return redirect()->route('admin.domains.index')->with('success', 'Domain updated successfully.');
    }

    public function destroy(Domain $domain)
    {
        $domain->delete();

        return redirect()->route('admin.domains.index')->with('success', 'Domain deleted successfully.');
    }

    public function storeHeroSlide(Request $request, Domain $domain)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $request->file('image')->store('domains/'.$domain->id.'/hero', 'public');

        $maxSort = $domain->heroSlides()->max('sort_order') ?? -1;

        $slide = $domain->heroSlides()->create([
            'image_path' => $path,
            'sort_order' => $maxSort + 1,
        ]);

        return response()->json([
            'success' => true,
            'slide' => [
                'id' => $slide->id,
                'url' => asset('storage/'.$slide->image_path),
                'is_active' => $slide->is_active,
                'sort_order' => $slide->sort_order,
            ],
        ]);
    }

    public function updateHeroSlide(Request $request, Domain $domain, DomainHeroSlide $slide)
    {
        $validated = $request->validate([
            'is_active' => 'nullable|boolean',
        ]);

        $slide->update($validated);

        return response()->json([
            'success' => true,
            'slide' => [
                'id' => $slide->id,
                'is_active' => $slide->is_active,
            ],
        ]);
    }

    public function destroyHeroSlide(Request $request, Domain $domain, DomainHeroSlide $slide)
    {
        Storage::disk('public')->delete($slide->image_path);
        $slide->delete();

        return response()->json(['success' => true]);
    }

    public function reorderHeroSlides(Request $request, Domain $domain)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:domain_hero_slides,id',
        ]);

        foreach ($request->input('order') as $index => $id) {
            DomainHeroSlide::where('id', $id)->where('domain_id', $domain->id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
