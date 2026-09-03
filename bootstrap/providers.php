<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\CustomPrivider::class,
    App\Providers\RepositoryServiceProvider::class,
    Yajra\DataTables\DataTablesServiceProvider::class,
    App\Modules\AppSupport\Providers\AppSupportServiceProvider::class,
    App\Modules\Media\Providers\MediaServiceProvider::class,
    App\Modules\BulkNotification\Providers\BulkNotificationServiceProvider::class,
    App\Modules\CMS\Providers\CMSServiceProvider::class,
    App\Modules\Chat\Providers\ChatServiceProvider::class,
    App\Modules\ActivityLog\Providers\ActivityLogServiceProvider::class,
    App\Modules\Post\Providers\PostServiceProvider::class,
];
