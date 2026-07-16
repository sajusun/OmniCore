<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Subscriber;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class SubscriberController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $subscribers = Subscriber::latest();

            return DataTables::of($subscribers)
                ->addIndexColumn()
                ->addColumn('email', fn($item) => '<strong>' . e($item->email) . '</strong>')
                ->addColumn('ip_address', fn($item) => $item->ip_address
                    ? '<code>' . e($item->ip_address) . '</code>'
                    : '<em class="text-muted">—</em>')
                ->addColumn('user_agent', fn($item) => $item->user_agent
                    ? '<small style="font-size:11px;">' . e(Str::limit($item->user_agent, 80)) . '</small>'
                    : '<em class="text-muted">—</em>')
                ->addColumn('subscribed_at', fn($item) => $item->subscribed_at->format('d M, Y h:i A'))
                ->rawColumns(['email', 'ip_address', 'user_agent'])
                ->make(true);
        }

        // Only return view if NOT ajax
        return view('backend.layouts.subscriber.index');
    }
}
