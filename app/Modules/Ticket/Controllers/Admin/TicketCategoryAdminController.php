<?php

declare(strict_types=1);

namespace App\Modules\Ticket\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Ticket\Models\TicketCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TicketCategoryAdminController extends Controller
{
    public function index(): View
    {
        $categories = TicketCategory::withCount('tickets')->orderBy('sort_order')->get();
        return view('Ticket::backend.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:ticket_categories,slug',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        TicketCategory::create($validated);

        return back()->with('success', 'Ticket category created successfully.');
    }

    public function update(Request $request, TicketCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:ticket_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:100',
            'is_active'   => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        if (!empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['slug']);
        }
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $category->update($validated);

        return back()->with('success', 'Ticket category updated successfully.');
    }

    public function destroy(TicketCategory $category): RedirectResponse
    {
        $category->delete();
        return back()->with('success', 'Ticket category deleted successfully.');
    }
}
