<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Customer::query()->withCount('bookings');

            return DataTables::of($query)
                ->addColumn('name', function ($customer) {
                    return '<div>
                        <div class="fw-semibold">'.e($customer->full_name).'</div>
                    </div>';
                })
                ->addColumn('auth_type', function ($customer) {
                    $badges = [];
                    if ($customer->hasPassword()) {
                        $badges[] = '<span class="badge bg-primary bg-opacity-10 text-primary me-1" style="font-size:0.7rem;">Email</span>';
                    }
                    if ($customer->isGoogleUser()) {
                        $badges[] = '<span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:0.7rem;">Google</span>';
                    }

                    return implode('', $badges);
                })
                ->addColumn('bookings_count_display', function ($customer) {
                    return $customer->bookings_count > 0
                        ? '<span class="badge bg-info bg-opacity-10 text-info">'.$customer->bookings_count.'</span>'
                        : '<span class="text-muted">0</span>';
                })
                ->addColumn('status', function ($customer) {
                    return $customer->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('last_login', function ($customer) {
                    return $customer->last_login_at
                        ? $customer->last_login_at->diffForHumans()
                        : '<span class="text-muted">Never</span>';
                })
                ->addColumn('action', function ($customer) {
                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="'.route('admin.customers.show', $customer).'"><i class="bx bx-show me-2"></i>View</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="'.route('admin.customers.update', $customer).'" method="POST">
                                    '.csrf_field().method_field('PUT').'
                                    <input type="hidden" name="is_active" value="'.($customer->is_active ? '0' : '1').'">
                                    <button type="submit" class="dropdown-item '.($customer->is_active ? 'text-danger' : 'text-success').'">
                                        <i class="bx bx-'.($customer->is_active ? 'block' : 'check-circle').' me-2"></i>
                                        '.($customer->is_active ? 'Deactivate' : 'Activate').'
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>';
                })
                ->rawColumns(['name', 'auth_type', 'bookings_count_display', 'status', 'last_login', 'action'])
                ->make(true);
        }

        return view('admin.customers.index');
    }

    public function show(Customer $customer)
    {
        $customer->loadCount('bookings');
        $bookings = $customer->bookings()
            ->with(['domain', 'hotel', 'roomType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.customers.show', compact('customer', 'bookings'));
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        $status = $customer->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Customer {$status} successfully.");
    }
}
