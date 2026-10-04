<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\ContactUs;
use Illuminate\Http\Request;
use App\Mail\ContactReplyMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class ContactUsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $queries = ContactUs::select(['id', 'subject', 'name', 'email', 'phone', 'message', 'is_read', 'created_at', 'read_at']);

            return DataTables::of($queries)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at
                        ? $row->created_at->format('d M, Y h:i A')
                        : 'N/A';
                })
                ->editColumn(
                    'is_read',
                    fn($row) => $row->is_read
                        ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Read</span>'
                        : '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Unread</span>'
                )
                ->editColumn('subject', fn($row) => '<span class="' . ($row->is_read ? '' : 'fw-semibold') . '">' . e($row->subject) . '</span>')
                ->editColumn('phone', fn($row) => $row->phone ? e($row->phone) : '<span class="text-muted">N/A</span>')
                ->addColumn('action', function ($row) {
                    return '<div class="d-flex align-items-center gap-1">'
                        . view('components.table.action', ['type' => 'view', 'href' => route('contact.me.show', $row->id)])->render()
                        . view('components.table.action', ['type' => 'delete', 'onclick' => "deleteContact({$row->id})"])->render()
                        . '</div>';
                })
                ->rawColumns(['is_read', 'subject', 'phone', 'action'])
                ->make(true);
        }

        $total = ContactUs::count();
        $unread = ContactUs::where('is_read', false)->count();

        return view('backend.contact.index', compact('total', 'unread'));
    }


    // Show single query via AJAX for modal
    public function show($id)
    {
        $contactUs = ContactUs::findOrFail($id);

        if (!$contactUs->is_read) {
            $contactUs->is_read = true;
            $contactUs->read_at = now();
            $contactUs->save();
        }

        return view('backend.contact.view', compact('contactUs'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contactUs = ContactUs::findOrFail($id);
        try {

            Mail::mailer('support')->to($contactUs->email)->send(
                new ContactReplyMail(
                    $request->subject,
                    $request->message,
                    $contactUs->name
                )
            );
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return back()->with('t-error', 'Email not Send.');
        }

        return back()->with('t-success', 'Email sent successfully.');
    }


    // Delete
    public function destroy($id)
    {
    //     if (! auth('web')->user()->can('delete_support_tickets')) {
    //     abort(403, 'You do not have permission to perform this action.');
    // }
        $contactUs = ContactUs::findOrFail($id);
        $contactUs->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully!'
        ]);
    }
}
