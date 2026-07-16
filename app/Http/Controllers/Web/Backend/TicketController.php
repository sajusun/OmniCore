<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user')->orderBy('created_at', 'desc')->get();
        return view('backend.tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::with(['user', 'messages.user'])->findOrFail($id);
        return view('backend.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);
        
        $ticket = Ticket::findOrFail($id);
        
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);
        
        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }
        
        return redirect()->back()->with('success', 'Reply sent successfully.');
    }

    public function close($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => 'closed']);
        
        return redirect()->back()->with('success', 'Ticket closed successfully.');
    }
}
