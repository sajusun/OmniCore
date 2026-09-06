<?php

return [
    App\Providers\AppServiceProvider::class,
    Yajra\DataTables\DataTablesServiceProvider::class,
    App\Modules\AppSupport\Providers\AppSupportServiceProvider::class,
    App\Modules\Media\Providers\MediaServiceProvider::class,
    App\Modules\Notification\Providers\NotificationServiceProvider::class,
    App\Modules\CMS\Providers\CMSServiceProvider::class,
    App\Modules\Chat\Providers\ChatServiceProvider::class,
    App\Modules\ActivityLog\Providers\ActivityLogServiceProvider::class,
    App\Modules\Post\Providers\PostServiceProvider::class,
    App\Modules\Social\Providers\SocialServiceProvider::class,
    App\Modules\Call\Providers\CallServiceProvider::class,
    App\Modules\Product\Providers\ProductServiceProvider::class,
    App\Modules\Cart\Providers\CartServiceProvider::class,
    App\Modules\Coupon\Providers\CouponServiceProvider::class,
    App\Modules\Order\Providers\OrderServiceProvider::class,
    App\Modules\Interaction\Providers\InteractionServiceProvider::class,
    App\Modules\Payment\Providers\PaymentServiceProvider::class,
    App\Modules\Auth\Providers\AuthServiceProvider::class,
];
