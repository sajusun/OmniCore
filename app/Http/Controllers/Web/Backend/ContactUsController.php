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
                        ? '<span class="badge bg-success">Read</span>'
                        : '<span class="badge bg-warning">Unread</span>'
                )
                ->addColumn('action', function ($row) {
                    $viewBtn = '<a href="' . route('contact.me.show', $row->id) . '" class="btn btn-sm btn-info view-btn me-1" data-id="' . $row->id . '"><i class="fe fe-eye"></i></a>';
                    $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '"><i class="fe fe-trash"></i></button>';
                    return '<div class="btn-list">' . $viewBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['is_read', 'action'])
                ->make(true);
        }

        return view('backend.contact.index');
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
