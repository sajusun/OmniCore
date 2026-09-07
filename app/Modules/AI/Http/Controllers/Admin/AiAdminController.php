<?php

namespace App\Modules\AI\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\AI\Models\AiConversation;
use App\Modules\AI\Models\AiKnowledgeBase;
use App\Modules\AI\Models\AiMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiAdminController extends Controller
{
    public function index(): View
    {
        $faqs = AiKnowledgeBase::latest()->paginate(15);
        $knowledgeBases = $faqs;
        $totalConversations = AiConversation::count();
        $totalMessages = AiMessage::count();
        $totalFaqs = AiKnowledgeBase::count();
        $totalTokens = AiConversation::sum('tokens_used');

        return view('ai::admin.knowledge_base.index', compact(
            'faqs',
            'knowledgeBases',
            'totalConversations',
            'totalMessages',
            'totalFaqs',
            'totalTokens'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'keywords' => 'nullable|string',
        ]);

        $keywords = $validated['keywords'] ? array_map('trim', explode(',', $validated['keywords'])) : [];

        AiKnowledgeBase::create([
            'category' => $validated['category'],
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'keywords' => $keywords,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Knowledge base entry created successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $kb = AiKnowledgeBase::findOrFail($id);

        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'keywords' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $keywords = $validated['keywords'] ? array_map('trim', explode(',', $validated['keywords'])) : [];

        $kb->update([
            'category' => $validated['category'],
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'keywords' => $keywords,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Knowledge base entry updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $kb = AiKnowledgeBase::findOrFail($id);
        $kb->delete();

        return redirect()->back()->with('success', 'Knowledge base entry deleted.');
    }
}
