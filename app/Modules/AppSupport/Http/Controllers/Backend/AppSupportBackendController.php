<?php

namespace App\Modules\AppSupport\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\AppSupport\Enums\SupportCategory;
use App\Modules\AppSupport\Enums\SupportStatus;
use App\Modules\AppSupport\Http\Requests\ReplySupportRequest;
use App\Modules\AppSupport\Models\AppSupport;
use App\Modules\AppSupport\Services\AppSupportService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AppSupportBackendController extends Controller
{
    public function __construct(
        protected AppSupportService $supportService
    ) {}

    /**
     * Display listing of all support reports.
     */
    public function index(Request $request): View
    {
        $filters = [
            'status'   => $request->get('status'),
            'category' => $request->get('category'),
            'search'   => $request->get('search'),
        ];

        $reports = $this->supportService->getAdminReports($filters, 15);

        // Calculate summary counters for quick filter tabs
        $counts = [
            'all'         => AppSupport::count(),
            'pending'     => AppSupport::where('status', SupportStatus::PENDING)->count(),
            'in_progress' => AppSupport::where('status', SupportStatus::IN_PROGRESS)->count(),
            'replied'     => AppSupport::where('status', SupportStatus::REPLIED)->count(),
            'resolved'    => AppSupport::where('status', SupportStatus::RESOLVED)->count(),
            'closed'      => AppSupport::where('status', SupportStatus::CLOSED)->count(),
        ];

        $categories = SupportCategory::cases();
        $types = array_map(fn($c) => $c->value, SupportCategory::cases());
        $statuses = SupportStatus::cases();
        $priorities = ['low', 'normal', 'high', 'urgent'];
        $supports = $reports;

        return view('app_support::backend.index', compact('reports', 'supports', 'counts', 'categories', 'types', 'statuses', 'priorities', 'filters'));
    }

    /**
     * View full details of a support report with replies & attachments.
     */
    public function show($id): View
    {
        $report = $this->supportService->getReportDetail($id);
        $statuses = SupportStatus::cases();

        return view('app_support::backend.show', compact('report', 'statuses'));
    }

    /**
     * Submit an admin reply to the report.
     */
    public function reply(ReplySupportRequest $request, $id): RedirectResponse
    {
        try {
            $report = AppSupport::findOrFail($id);
            $admin = Auth::user();

            $this->supportService->sendAdminReply(
                support: $report,
                message: $request->validated('message'),
                admin: $admin,
                newStatus: $request->validated('status'),
                attachments: $request->file('attachments')
            );

            return redirect()->route('admin.app-supports.show', $report->id)
                ->with('success', 'Reply sent successfully! User has been notified via In-App Notification and Email.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send reply: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update report status (e.g. resolve or close).
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,replied,resolved,closed',
        ]);

        try {
            $report = AppSupport::findOrFail($id);
            $this->supportService->updateStatus($report, $request->status);

            return redirect()->back()
                ->with('success', 'Status updated to ' . ucfirst($request->status) . ' successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
}
