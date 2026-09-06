<?php

namespace App\Modules\Payment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Models\PaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GatewaySettingController extends Controller
{
    public function index(): View
    {
        $gateways = PaymentGateway::orderBy('sort_order', 'asc')->get();
        return view('payment::backend.gateways.index', compact('gateways'));
    }

    public function update(Request $request, PaymentGateway $gateway): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
            'is_sandbox' => 'nullable|boolean',
            'fee_fixed' => 'required|numeric|min:0',
            'fee_percent' => 'required|numeric|min:0|max:100',
            'credentials' => 'nullable|array',
        ]);

        $gateway->name = $validated['name'];
        $gateway->is_active = $request->boolean('is_active');
        $gateway->is_sandbox = $request->boolean('is_sandbox');
        $gateway->fee_fixed = $validated['fee_fixed'];
        $gateway->fee_percent = $validated['fee_percent'];

        if ($request->has('credentials')) {
            $gateway->credentials = $request->input('credentials');
        }

        $gateway->save();

        return back()->with('success', "{$gateway->name} settings updated successfully.");
    }
}
