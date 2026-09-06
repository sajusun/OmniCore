<?php

declare(strict_types=1);

namespace App\Modules\Vendor\Services;

use App\Models\User;
use App\Modules\Notification\Services\NotificationService;
use App\Modules\Payment\Services\WalletService;
use App\Modules\Vendor\Enums\PayoutStatus;
use App\Modules\Vendor\Enums\VendorStatus;
use App\Modules\Vendor\Models\VendorMember;
use App\Modules\Vendor\Models\VendorPayout;
use App\Modules\Vendor\Models\VendorStore;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class VendorService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected WalletService $walletService
    ) {}

    /**
     * Register a new vendor store.
     */
    public function registerStore(
        User $user,
        array $data,
        ?UploadedFile $logo = null,
        ?UploadedFile $banner = null
    ): VendorStore {
        return DB::transaction(function () use ($user, $data, $logo, $banner) {
            $logoPath = $logo ? $logo->store('vendors/logos', 'public') : null;
            $bannerPath = $banner ? $banner->store('vendors/banners', 'public') : null;

            $slug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

            $store = VendorStore::create([
                'user_id'            => $user->id,
                'name'               => $data['name'],
                'slug'               => $slug,
                'description'        => $data['description'] ?? null,
                'logo_path'          => $logoPath,
                'banner_path'        => $bannerPath,
                'phone'              => $data['phone'] ?? null,
                'email'              => $data['email'] ?? $user->email,
                'address'            => $data['address'] ?? null,
                'commission_rate'    => (float) ($data['commission_rate'] ?? 10.00),
                'status'             => VendorStatus::ACTIVE->value,
                'payout_settings'    => $data['payout_settings'] ?? null,
                'business_documents' => $data['business_documents'] ?? null,
            ]);

            // Add owner member record
            VendorMember::create([
                'vendor_id'   => $store->id,
                'user_id'     => $user->id,
                'role'        => 'owner',
                'permissions' => ['all'],
            ]);

            // Notify admins
            $admins = User::role('Super Admin')->get();
            if ($admins->isNotEmpty()) {
                $this->notificationService->sendMany(
                    $admins,
                    title: "New Vendor Store Registered: {$store->name}",
                    body: "User {$user->name} created store {$store->name}.",
                    type: 'vendor',
                    referenceType: 'vendor',
                    referenceId: $store->id,
                    meta: ['store_id' => $store->id]
                );
            }

            return $store->fresh(['owner', 'members']);
        });
    }

    /**
     * Update vendor store profile.
     */
    public function updateStore(
        VendorStore $store,
        array $data,
        ?UploadedFile $logo = null,
        ?UploadedFile $banner = null
    ): VendorStore {
        $updates = [
            'name'        => $data['name'] ?? $store->name,
            'description' => $data['description'] ?? $store->description,
            'phone'       => $data['phone'] ?? $store->phone,
            'email'       => $data['email'] ?? $store->email,
            'address'     => $data['address'] ?? $store->address,
        ];

        if (!empty($data['slug'])) {
            $updates['slug'] = Str::slug($data['slug']);
        }

        if ($logo) {
            $updates['logo_path'] = $logo->store('vendors/logos', 'public');
        }

        if ($banner) {
            $updates['banner_path'] = $banner->store('vendors/banners', 'public');
        }

        if (isset($data['payout_settings'])) {
            $updates['payout_settings'] = $data['payout_settings'];
        }

        $store->update($updates);

        return $store->fresh();
    }

    /**
     * Record completed sale and calculate vendor share.
     */
    public function recordSale(VendorStore $store, float $orderTotal): void
    {
        DB::transaction(function () use ($store, $orderTotal) {
            $platformFee = round(($orderTotal * (float) $store->commission_rate) / 100, 2);
            $vendorShare = $orderTotal - $platformFee;

            $store->update([
                'total_sales'    => (float) $store->total_sales + $orderTotal,
                'total_earnings' => (float) $store->total_earnings + $vendorShare,
                'balance'        => (float) $store->balance + $vendorShare,
            ]);

            // Notify store owner
            $this->notificationService->send(
                $store->owner,
                title: "Sale recorded for {$store->name}",
                body: "Order of $" . number_format($orderTotal, 2) . " processed. Your share of $" . number_format($vendorShare, 2) . " has been credited to store balance.",
                type: 'vendor_sale',
                referenceType: 'vendor',
                referenceId: $store->id,
                meta: ['store_id' => $store->id, 'vendor_share' => $vendorShare]
            );
        });
    }

    /**
     * Request payout from store balance.
     */
    public function requestPayout(
        VendorStore $store,
        User $requester,
        float $amount,
        string $method = 'wallet'
    ): VendorPayout {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Payout amount must be greater than zero.');
        }

        if ((float) $store->balance < $amount) {
            throw new InvalidArgumentException('Insufficient store balance for payout.');
        }

        return DB::transaction(function () use ($store, $requester, $amount, $method) {
            $store->update([
                'balance' => (float) $store->balance - $amount,
            ]);

            $isWallet = $method === 'wallet';

            $payout = VendorPayout::create([
                'vendor_id'      => $store->id,
                'requested_by'   => $requester->id,
                'amount'         => $amount,
                'status'         => $isWallet ? PayoutStatus::COMPLETED->value : PayoutStatus::PENDING->value,
                'payout_method'  => $method,
                'transaction_id' => 'PO-' . strtoupper(Str::random(8)),
                'processed_at'   => $isWallet ? now() : null,
            ]);

            if ($isWallet) {
                // Instantly credit to user's wallet
                $this->walletService->deposit(
                    $store->owner,
                    $amount,
                    $payout,
                    "Vendor store payout for {$store->name}"
                );
            }

            // Send notification
            $this->notificationService->send(
                $store->owner,
                title: "Vendor Payout: $" . number_format($amount, 2),
                body: $isWallet ? "Payout successfully deposited directly into your Wallet." : "Payout request submitted for admin review.",
                type: 'vendor_payout',
                referenceType: 'vendor_payout',
                referenceId: $payout->id,
                meta: ['payout_id' => $payout->id, 'amount' => $amount]
            );

            return $payout;
        });
    }

    /**
     * Approve or update store status.
     */
    public function updateStatus(VendorStore $store, VendorStatus|string $status): VendorStore
    {
        $statusVal = $status instanceof VendorStatus ? $status->value : $status;
        $store->update(['status' => $statusVal]);

        $this->notificationService->send(
            $store->owner,
            title: "Store status updated: {$store->name}",
            body: "Your store status is now: " . ucfirst($statusVal),
            type: 'vendor_status',
            referenceType: 'vendor',
            referenceId: $store->id,
            meta: ['store_id' => $store->id, 'status' => $statusVal]
        );

        return $store->fresh();
    }
}
