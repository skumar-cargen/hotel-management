<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CareerApplicationResource;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CareerApplicationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CareerApplication::with(['career', 'domain']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('career_id')) {
                $query->where('career_id', $request->career_id);
            }

            return DataTables::of($query)
                ->addColumn('career_title', function ($app) {
                    return '<a href="'.route('admin.careers.edit', $app->career_id).'" class="text-primary fw-semibold">'
                        .e($app->career->title ?? 'N/A').'</a>';
                })
                ->addColumn('domain_name', function ($app) {
                    return '<span class="badge bg-secondary">'.e($app->domain->name ?? 'N/A').'</span>';
                })
                ->addColumn('applicant_info', function ($app) {
                    return '<div class="fw-semibold">'.e($app->name).'</div>'
                        .'<div class="text-muted" style="font-size:.78rem;">'.e($app->email).'</div>';
                })
                ->addColumn('phone_display', function ($app) {
                    return '<a href="tel:'.e($app->phone).'" class="text-dark">'.e($app->phone).'</a>';
                })
                ->addColumn('status_badge', function ($app) {
                    $colors = [
                        'new' => 'primary',
                        'reviewed' => 'info',
                        'shortlisted' => 'success',
                        'rejected' => 'danger',
                    ];
                    $color = $colors[$app->status->value] ?? 'secondary';

                    return '<span class="badge bg-'.$color.'">'.ucfirst($app->status->value).'</span>';
                })
                ->addColumn('applied_at', function ($app) {
                    return '<span style="font-size:.82rem;">'.$app->created_at->format('M j, Y').'</span>'
                        .'<br><span class="text-muted" style="font-size:.72rem;">'.$app->created_at->diffForHumans().'</span>';
                })
                ->addColumn('action', function ($app) {
                    $viewBtn = '<a class="dropdown-item" href="'.route('admin.career-applications.show', $app).'"><i class="bx bx-show me-2"></i>View Details</a>';

                    $resumeBtn = '';
                    if ($app->resume_path) {
                        $resumeBtn = '<a class="dropdown-item" href="'.asset('storage/'.$app->resume_path).'" target="_blank"><i class="bx bx-download me-2"></i>Download Resume</a>';
                    }

                    return '<div class="dropdown action-dropdown">
                        <button class="btn btn-action-toggle" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>'.$viewBtn.'</li>
                            '.($resumeBtn ? '<li>'.$resumeBtn.'</li>' : '').'
                        </ul>
                    </div>';
                })
                ->rawColumns(['career_title', 'domain_name', 'applicant_info', 'phone_display', 'status_badge', 'applied_at', 'action'])
                ->make(true);
        }

        return view('admin.career-applications.index');
    }

    public function show(CareerApplication $careerApplication)
    {
        $careerApplication->load(['career.domains', 'domain']);

        return view('admin.career-applications.show', compact('careerApplication'));
    }

    public function update(Request $request, CareerApplication $careerApplication)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,reviewed,shortlisted,rejected',
        ]);

        $careerApplication->update($validated);

        return redirect()->back()->with('success', 'Application status updated to '.ucfirst($validated['status']).'.');
    }

    // ─── JSON API Methods ─────────────────────────────────────────

    public function apiIndex(Request $request)
    {
        $query = CareerApplication::with(['career', 'domain'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('career_id'), fn ($q) => $q->where('career_id', $request->career_id))
            ->when($request->filled('domain_id'), fn ($q) => $q->where('domain_id', $request->domain_id))
            ->latest();

        $applications = $query->paginate($request->integer('per_page', 25));

        return CareerApplicationResource::collection($applications);
    }

    public function apiShow(CareerApplication $careerApplication)
    {
        $careerApplication->load(['career', 'domain']);

        return new CareerApplicationResource($careerApplication);
    }

    public function apiUpdateStatus(Request $request, CareerApplication $careerApplication)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,reviewed,shortlisted,rejected',
        ]);

        $careerApplication->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated to '.ucfirst($validated['status']).'.',
            'data' => new CareerApplicationResource($careerApplication->load(['career', 'domain'])),
        ]);
    }
}
