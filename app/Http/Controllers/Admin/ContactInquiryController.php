<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use App\Traits\ScopesByDomain;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContactInquiryController extends Controller
{
    use ScopesByDomain;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ContactInquiry::query()->with(['domain', 'hotel']);

            $user = $request->user();
            if (! $user->isAdmin()) {
                $domainIds = $user->managedDomainIds();
                $query->whereIn('domain_id', $domainIds);
            }

            return DataTables::of($query)
                ->addColumn('domain_name', fn ($i) => $i->domain->name ?? '-')
                ->addColumn('hotel_name', fn ($i) => $i->hotel->name ?? '-')
                ->addColumn('status_badge', function ($i) {
                    return match ($i->status->value) {
                        'read' => '<span class="badge bg-info">Read</span>',
                        'replied' => '<span class="badge bg-success">Replied</span>',
                        default => '<span class="badge bg-warning">New</span>',
                    };
                })
                ->editColumn('created_at', fn ($i) => $i->created_at?->format('M d, Y H:i'))
                ->addColumn('action', function ($i) {
                    return '<a href="'.route('admin.contact-inquiries.show', $i).'" class="btn btn-sm btn-outline-primary"><i class="bx bx-show me-1"></i>View</a>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('admin.contact-inquiries.index');
    }

    public function show(ContactInquiry $contactInquiry)
    {
        $contactInquiry->load(['domain', 'hotel']);

        return view('admin.contact-inquiries.show', compact('contactInquiry'));
    }

    public function update(Request $request, ContactInquiry $contactInquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,replied',
        ]);

        $contactInquiry->update($validated);

        return back()->with('success', 'Status updated successfully.');
    }
}
