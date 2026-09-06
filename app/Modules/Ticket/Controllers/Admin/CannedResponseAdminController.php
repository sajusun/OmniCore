<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Ticket\Models\CannedResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CannedResponseAdminController extends Controller
{
    public function index(): View
    {
        $cannedResponses = CannedResponse::orderBy('created_at', 'desc')->get();
        return view('Ticket::backend.canned-responses.index', compact('cannedResponses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'shortcut'  => 'nullable|string|max:50|unique:canned_responses,shortcut',
            'body'      => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        CannedResponse::create($validated);

        return back()->with('success', 'Canned response template created.');
    }

    public function update(Request $request, CannedResponse $cannedResponse): RedirectResponse
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'shortcut'  => 'nullable|string|max:50|unique:canned_responses,shortcut,' . $cannedResponse->id,
            'body'      => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $cannedResponse->update($validated);

        return back()->with('success', 'Canned response updated.');
    }

    public function destroy(CannedResponse $cannedResponse): RedirectResponse
    {
        $cannedResponse->delete();
        return back()->with('success', 'Canned response deleted.');
    }
}
