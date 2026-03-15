<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Traits\ScopesByDomain;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;

class NewsletterSubscriberController extends Controller
{
    use ScopesByDomain;

    public function index(Request $request)
    {
        // CSV Export
        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportCsv($request);
        }

        if ($request->ajax()) {
            $query = NewsletterSubscriber::query()->with('domain')->latest('subscribed_at');

            $user = $request->user();
            if (! $user->isAdmin()) {
                $domainIds = $user->managedDomainIds();
                $query->whereIn('domain_id', $domainIds);
            }

            if ($request->filled('search_custom')) {
                $search = $request->search_custom;
                $query->where('email', 'like', "%{$search}%");
            }

            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            if ($request->filled('domain_id')) {
                $query->where('domain_id', $request->domain_id);
            }

            return DataTables::of($query)
                ->addColumn('domain_name', fn ($s) => $s->domain->name ?? '-')
                ->addColumn('status_badge', function ($s) {
                    return $s->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-secondary">Unsubscribed</span>';
                })
                ->editColumn('subscribed_at', fn ($s) => $s->subscribed_at?->format('M d, Y H:i'))
                ->editColumn('unsubscribed_at', fn ($s) => $s->unsubscribed_at?->format('M d, Y H:i') ?? '-')
                ->addColumn('action', function ($s) {
                    $toggleBtn = $s->is_active
                        ? '<button class="btn btn-sm btn-outline-warning toggle-btn" data-id="'.$s->id.'" data-status="0" title="Unsubscribe"><i class="bx bx-block me-1"></i>Unsub</button>'
                        : '<button class="btn btn-sm btn-outline-success toggle-btn" data-id="'.$s->id.'" data-status="1" title="Reactivate"><i class="bx bx-check me-1"></i>Activate</button>';
                    $deleteBtn = '<button class="btn btn-sm btn-outline-danger delete-btn ms-1" data-id="'.$s->id.'" title="Delete"><i class="bx bx-trash"></i></button>';
                    return $toggleBtn . $deleteBtn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('admin.newsletter-subscribers.index');
    }

    public function update(Request $request, NewsletterSubscriber $newsletterSubscriber)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $newsletterSubscriber->update([
            'is_active' => $validated['is_active'],
            'unsubscribed_at' => $validated['is_active'] ? null : now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Subscriber updated successfully.']);
    }

    public function destroy(NewsletterSubscriber $newsletterSubscriber)
    {
        $newsletterSubscriber->delete();

        return response()->json(['success' => true, 'message' => 'Subscriber deleted successfully.']);
    }

    private function exportCsv(Request $request): StreamedResponse
    {
        $query = NewsletterSubscriber::query()->with('domain')->latest('subscribed_at');

        $user = $request->user();
        if (! $user->isAdmin()) {
            $query->whereIn('domain_id', $user->managedDomainIds());
        }

        if ($request->filled('search_custom')) {
            $query->where('email', 'like', "%{$request->search_custom}%");
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('domain_id')) {
            $query->where('domain_id', $request->domain_id);
        }

        $subscribers = $query->get();

        return response()->streamDownload(function () use ($subscribers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Domain', 'Status', 'Subscribed At', 'Unsubscribed At', 'IP Address']);
            foreach ($subscribers as $s) {
                fputcsv($handle, [
                    $s->email,
                    $s->domain->name ?? '-',
                    $s->is_active ? 'Active' : 'Unsubscribed',
                    $s->subscribed_at?->format('Y-m-d H:i:s'),
                    $s->unsubscribed_at?->format('Y-m-d H:i:s') ?? '',
                    $s->ip_address ?? '',
                ]);
            }
            fclose($handle);
        }, 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
