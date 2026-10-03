<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Vendor\Http\Requests\RegisterVendorStoreRequest;
use App\Modules\Vendor\Http\Requests\RequestVendorPayoutRequest;
use App\Modules\Vendor\Http\Requests\UpdateVendorStoreRequest;
use App\Modules\Vendor\Models\VendorStore;
use App\Modules\Vendor\Resources\VendorPayoutResource;
use App\Modules\Vendor\Resources\VendorStoreResource;
use App\Modules\Vendor\Services\VendorService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class VendorApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected VendorService $vendorService
    ) {}

    /**
     * List active vendor stores (Public).
     */
    public function index(Request $request): JsonResponse
    {
        $query = VendorStore::active()->orderBy('is_featured', 'desc')->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });
        }

        $stores = $query->paginate($request->integer('per_page', 15));

        return $this->paginated(
            $stores,
            VendorStoreResource::class,
            'Vendor stores retrieved successfully.'
        );
    }

    /**
     * Show single vendor store profile (Public).
     */
    public function show(string $slug): JsonResponse
    {
        $store = VendorStore::where('slug', $slug)->orWhere('id', $slug)->first();

        if (!$store || $store->status->value !== 'active') {
            return $this->notFound('Vendor store not found or not active.');
        }

        return $this->success(
            new VendorStoreResource($store),
            'Vendor store details retrieved successfully.'
        );
    }

    /**
     * Get current user's vendor store.
     */
    public function myStore(Request $request): JsonResponse
    {
        $store = $request->user()->vendorStore;

        if (!$store) {
            return $this->notFound('You do not have a vendor store registered yet.');
        }

        return $this->success(
            new VendorStoreResource($store),
            'My vendor store retrieved successfully.'
        );
    }

    /**
     * Register a new vendor store.
     */
    public function register(RegisterVendorStoreRequest $request): JsonResponse
    {
        if ($request->user()->vendorStore) {
            return $this->error('You already have a vendor store registered.', 422);
        }

        $store = $this->vendorService->registerStore(
            $request->user(),
            $request->validated(),
            $request->file('logo'),
            $request->file('banner')
        );

        return $this->created(
            new VendorStoreResource($store),
            'Vendor store registered successfully.'
        );
    }

    /**
     * Update current user's vendor store.
     */
    public function update(UpdateVendorStoreRequest $request): JsonResponse
    {
        $store = $request->user()->vendorStore;

        if (!$store) {
            return $this->notFound('Vendor store not found.');
        }

        $store = $this->vendorService->updateStore(
            $store,
            $request->validated(),
            $request->file('logo'),
            $request->file('banner')
        );

        return $this->success(
            new VendorStoreResource($store),
            'Vendor store updated successfully.'
        );
    }

    /**
     * List vendor store payouts.
     */
    public function payouts(Request $request): JsonResponse
    {
        $store = $request->user()->vendorStore;
        if (!$store) {
            return $this->notFound('Vendor store not found.');
        }

        $payouts = $store->payouts()->orderBy('created_at', 'desc')->paginate($request->integer('per_page', 15));

        return $this->paginated(
            $payouts,
            VendorPayoutResource::class,
            'Vendor payouts retrieved successfully.'
        );
    }

    /**
     * Request a store payout to Wallet.
     */
    public function requestPayout(Request $request): JsonResponse
    {
        $store = $request->user()->vendorStore;
        if (!$store) {
            return $this->notFound('Vendor store not found.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'method' => 'nullable|string|in:wallet,bank_transfer',
        ]);

        try {
            $payout = $this->vendorService->requestPayout(
                $store,
                $request->user(),
                (float) $validated['amount'],
                $validated['method'] ?? 'wallet'
            );

            return $this->success(
                new VendorPayoutResource($payout),
                'Payout request of $' . number_format((float) $validated['amount'], 2) . ' processed successfully.'
            );
        } catch (InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
