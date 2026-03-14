<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::query()->with(['roles', 'domains']);

            return DataTables::of($query)
                ->addColumn('role', function ($user) {
                    $role = $user->roles->first();

                    return $role
                        ? '<span class="badge bg-primary">'.e($role->name).'</span>'
                        : '<span class="badge bg-secondary">No Role</span>';
                })
                ->addColumn('managed_domains', function ($user) {
                    if ($user->hasRole('Admin')) {
                        return '<span class="badge bg-info bg-opacity-10 text-info">All Domains</span>';
                    }
                    $domains = $user->domains;
                    if ($domains->isEmpty()) {
                        return '<span class="text-muted" style="font-size:0.8rem;">—</span>';
                    }

                    return $domains->take(2)->map(function ($d) {
                        return '<span class="badge bg-success bg-opacity-10 text-success me-1" style="font-size:0.7rem;">'.e($d->name).'</span>';
                    })->implode('').($domains->count() > 2 ? '<span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:0.7rem;">+'.($domains->count() - 2).'</span>' : '');
                })
                ->addColumn('status', function ($user) {
                    return $user->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('last_login', function ($user) {
                    return $user->last_login_at
                        ? $user->last_login_at->diffForHumans()
                        : 'Never';
                })
                ->addColumn('action', function ($user) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.users.edit', $user).'"><i class="bx bx-edit-alt me-2"></i>Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form action="'.route('admin.users.destroy', $user).'" method="POST">'.csrf_field().method_field('DELETE').'<button type="button" class="dropdown-item text-danger" data-confirm-delete="Are you sure you want to delete this item?"><i class="bx bx-trash me-2"></i>Delete</button></form></li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['role', 'managed_domains', 'status', 'action'])
                ->make(true);
        }

        return view('admin.users.index');
    }

    public function create()
    {
        return view('admin.users.edit', [
            'user' => new User,
            'roles' => Role::all(),
            'domains' => Domain::active()->orderBy('name')->get(),
            'userDomains' => [],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|exists:roles,name',
            'domains' => 'nullable|array',
            'domains.*' => 'exists:domains,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($validated['role']);

        // Sync managed domains
        if (! empty($validated['domains'])) {
            $user->domains()->sync($validated['domains']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'domains']);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $userRole = $user->roles->first()?->name;
        $domains = Domain::active()->orderBy('name')->get();
        $userDomains = $user->domains->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRole', 'domains', 'userDomains'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:50',
            'role' => 'required|exists:roles,name',
            'domains' => 'nullable|array',
            'domains.*' => 'exists:domains,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles([$validated['role']]);

        // Sync managed domains
        $user->domains()->sync($validated['domains'] ?? []);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
