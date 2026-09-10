<?php

namespace Modules\AiAssistant\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\AiAssistant\Models\AiPersona;

class AiPersonaController extends Controller
{
    public function index(): JsonResponse
    {
        $personas = AiPersona::where('user_id', Auth::id())
            ->orWhere('is_system_default', true)
            ->get();

        return response()->json($personas);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:80',
            'icon'          => 'nullable|string|max:30',
            'model'         => 'required|string',
            'system_prompt' => 'required|string',
            'temperature'   => 'nullable|numeric|between:0.0,1.0',
        ]);

        $persona = AiPersona::create([
            'user_id'           => Auth::id(),
            'name'              => $validated['name'],
            'slug'              => Str::slug($validated['name']) . '-' . Str::random(5),
            'icon'              => $validated['icon'] ?? 'terminal',
            'model'             => $validated['model'],
            'system_prompt'     => $validated['system_prompt'],
            'temperature'       => $validated['temperature'] ?? 0.70,
            'is_system_default' => false,
        ]);

        return response()->json($persona, 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $persona = AiPersona::where('user_id', Auth::id())->findOrFail($id);
        $persona->delete();

        return response()->json(['status' => 'deleted']);
    }
}
