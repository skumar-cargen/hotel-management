<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Role::withCount(['permissions', 'users'])->latest();

            return DataTables::of($query)
                ->addColumn('permissions_list', function ($role) {
                    $badges = $role->permissions->take(4)->map(function ($p) {
                        return '<span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1" style="font-size:0.7rem;">'.e($p->name).'</span>';
                    })->implode('');
                    if ($role->permissions->count() > 4) {
                        $badges .= '<span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:0.7rem;">+'.($role->permissions->count() - 4).' more</span>';
                    }

                    return $badges ?: '<span class="text-muted" style="font-size:0.8rem;">No permissions</span>';
                })
                ->addColumn('users_badge', function ($role) {
                    return '<span class="badge bg-info bg-opacity-10 text-info">'.$role->users_count.' users</span>';
                })
                ->addColumn('action', function ($role) {
                    $html = '<div class="dropdown action-dropdown">';
                    $html .= '<button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>';
                    $html .= '<ul class="dropdown-menu dropdown-menu-end">';
                    $html .= '<li><a class="dropdown-item" href="'.route('admin.roles.edit', $role).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>';
                    if (! in_array($role->name, ['Admin'])) {
                        $html .= '<li><hr class="dropdown-divider"></li>';
                        $html .= '<li><form action="'.route('admin.roles.destroy', $role).'" method="POST">'
                            .csrf_field().method_field('DELETE')
                            .'<button type="button" class="dropdown-item text-danger" data-confirm-delete="This will remove the role from all users. Are you sure?"><i class="bx bx-trash me-2"></i>Delete</button>'
                            .'</form></li>';
                    }
                    $html .= '</ul></div>';

                    return $html;
                })
                ->rawColumns(['permissions_list', 'users_badge', 'action'])
                ->make(true);
        }

        return view('admin.roles.index');
    }

    public function create()
    {
        $role = new Role;
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            return $this->getPermissionCategory($permission->name);
        });
        $rolePermissions = [];

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions(Permission::whereIn('id', $validated['permissions'])->get());
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role "'.$role->name.'" created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            return $this->getPermissionCategory($permission->name);
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);

        $permissionModels = ! empty($validated['permissions'])
            ? Permission::whereIn('id', $validated['permissions'])->get()
            : collect();
        $role->syncPermissions($permissionModels);

        return redirect()->route('admin.roles.index')->with('success', 'Role "'.$role->name.'" updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot delete the Admin role.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    /**
     * Group permissions by category based on their name.
     */
    private function getPermissionCategory(string $permissionName): string
    {
        $map = [
            'domains' => 'Domains',
            'locations' => 'Locations',
            'hotels' => 'Hotels',
            'rooms' => 'Room Types',
            'pricing' => 'Pricing',
            'bookings' => 'Bookings',
            'users' => 'Users',
            'reviews' => 'Reviews',
            'analytics' => 'Analytics',
            'currencies' => 'Currencies',
            'pages' => 'Pages & SEO',
            'seo' => 'Pages & SEO',
            'settings' => 'Settings',
        ];

        foreach ($map as $keyword => $category) {
            if (str_contains($permissionName, $keyword)) {
                return $category;
            }
        }

        return 'Other';
    }
}
