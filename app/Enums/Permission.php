<?php

namespace App\Enums;

enum Permission: string
{
    // ─── Dashboard ────────────────────────────────────────────────
    case DASHBOARD_ACCESS = 'dashboard.access';

    // ─── User Management ──────────────────────────────────────────
    case USER_LIST   = 'user.list';
    case USER_CREATE = 'user.create';
    case USER_EDIT   = 'user.edit';
    case USER_DELETE = 'user.delete';
    case USER_STATUS = 'user.status';

    // ─── Staff / Admin Management ─────────────────────────────────
    case STAFF_LIST   = 'staff.list';
    case STAFF_CREATE = 'staff.create';
    case STAFF_EDIT   = 'staff.edit';
    case STAFF_DELETE = 'staff.delete';

    // ─── Role Management ──────────────────────────────────────────
    case ROLE_LIST   = 'role.list';
    case ROLE_CREATE = 'role.create';
    case ROLE_EDIT   = 'role.edit';
    case ROLE_DELETE = 'role.delete';

    // ─── Permission Management ────────────────────────────────────
    case PERMISSION_LIST   = 'permission.list';
    case PERMISSION_CREATE = 'permission.create';
    case PERMISSION_EDIT   = 'permission.edit';
    case PERMISSION_DELETE = 'permission.delete';

    // ─── Settings ─────────────────────────────────────────────────
    case SETTINGS_ACCESS = 'settings.access';
    case SETTINGS_EDIT   = 'settings.edit';

    // ─── Media ────────────────────────────────────────────────────
    case MEDIA_MANAGE = 'media.manage';

    // ─── Support / Tickets ────────────────────────────────────────
    case TICKET_LIST  = 'ticket.list';
    case TICKET_REPLY = 'ticket.reply';
    case TICKET_CLOSE = 'ticket.close';

    // ─── App Support & Feedback ───────────────────────────────────
    case APP_SUPPORT_LIST   = 'app.support.list';
    case APP_SUPPORT_REPLY  = 'app.support.reply';
    case APP_SUPPORT_STATUS = 'app.support.status';

    // ─── Contact Us ───────────────────────────────────────────────
    case SUPPORT_CENTER_MANAGE = 'support.center.manage';

    // ─── Notifications ────────────────────────────────────────────
    case NOTIFICATION_SEND = 'notification.send';

    // ─── Activity Logs ────────────────────────────────────────────
    case ACTIVITY_LOG_VIEW = 'activity.log.view';

    // ─── CMS / Pages ──────────────────────────────────────────────
    case CMS_MANAGE = 'cms.manage';

    // ─── Optimization ─────────────────────────────────────────────
    case SYSTEM_OPTIMIZE = 'system.optimize';

    // ─────────────────────────────────────────────────────────────
    // Human-readable display name for UI
    // ─────────────────────────────────────────────────────────────
    public function label(): string
    {
        return match($this) {
            self::DASHBOARD_ACCESS    => 'Dashboard Access',

            self::USER_LIST           => 'User List',
            self::USER_CREATE         => 'User Create',
            self::USER_EDIT           => 'User Edit',
            self::USER_DELETE         => 'User Delete',
            self::USER_STATUS         => 'User Status',

            self::STAFF_LIST          => 'Staff List',
            self::STAFF_CREATE        => 'Staff Create',
            self::STAFF_EDIT          => 'Staff Edit',
            self::STAFF_DELETE        => 'Staff Delete',

            self::ROLE_LIST           => 'Role List',
            self::ROLE_CREATE         => 'Role Create',
            self::ROLE_EDIT           => 'Role Edit',
            self::ROLE_DELETE         => 'Role Delete',

            self::PERMISSION_LIST     => 'Permission List',
            self::PERMISSION_CREATE   => 'Permission Create',
            self::PERMISSION_EDIT     => 'Permission Edit',
            self::PERMISSION_DELETE   => 'Permission Delete',

            self::SETTINGS_ACCESS     => 'Settings Access',
            self::SETTINGS_EDIT       => 'Settings Edit',

            self::MEDIA_MANAGE        => 'Media Manage',

            self::TICKET_LIST         => 'Ticket List',
            self::TICKET_REPLY        => 'Ticket Reply',
            self::TICKET_CLOSE        => 'Ticket Close',

            self::APP_SUPPORT_LIST    => 'App Support List',
            self::APP_SUPPORT_REPLY   => 'App Support Reply',
            self::APP_SUPPORT_STATUS  => 'App Support Status',

            self::SUPPORT_CENTER_MANAGE => 'Support Center Manage',

            self::NOTIFICATION_SEND   => 'Notification Send',

            self::ACTIVITY_LOG_VIEW   => 'Activity Log View',

            self::CMS_MANAGE          => 'CMS Manage',

            self::SYSTEM_OPTIMIZE     => 'System Optimize',
        };
    }

    /**
     * Group name for UI grouping (e.g. in role edit page)
     */
    public function group(): string
    {
        return match($this) {
            self::DASHBOARD_ACCESS                              => 'Dashboard',
            self::USER_LIST, self::USER_CREATE,
            self::USER_EDIT, self::USER_DELETE,
            self::USER_STATUS                                   => 'Users',
            self::STAFF_LIST, self::STAFF_CREATE,
            self::STAFF_EDIT, self::STAFF_DELETE                => 'Staff',
            self::ROLE_LIST, self::ROLE_CREATE,
            self::ROLE_EDIT, self::ROLE_DELETE                  => 'Roles',
            self::PERMISSION_LIST, self::PERMISSION_CREATE,
            self::PERMISSION_EDIT, self::PERMISSION_DELETE      => 'Permissions',
            self::SETTINGS_ACCESS, self::SETTINGS_EDIT          => 'Settings',
            self::MEDIA_MANAGE                                   => 'Media',
            self::TICKET_LIST, self::TICKET_REPLY,
            self::TICKET_CLOSE                                   => 'Tickets',
            self::APP_SUPPORT_LIST, self::APP_SUPPORT_REPLY,
            self::APP_SUPPORT_STATUS                             => 'App Support',
            self::SUPPORT_CENTER_MANAGE                          => 'Support Center',
            self::NOTIFICATION_SEND                              => 'Notifications',
            self::ACTIVITY_LOG_VIEW                              => 'Activity Logs',
            self::CMS_MANAGE                                     => 'CMS',
            self::SYSTEM_OPTIMIZE                                => 'System',
        };
    }

    /**
     * All permission values as a flat array of strings
     */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Return [ 'name' => value, 'display_name' => label ] array for seeding
     */
    public static function forSeeding(): array
    {
        return array_map(
            fn(self $p) => ['name' => $p->value, 'display_name' => $p->label()],
            self::cases()
        );
    }

    /**
     * Get grouped permissions for UI rendering
     * Returns: [ 'Group Name' => [ Permission, ... ], ... ]
     */
    public static function grouped(): array
    {
        $groups = [];
        foreach (self::cases() as $permission) {
            $groups[$permission->group()][] = $permission;
        }
        return $groups;
    }
}
