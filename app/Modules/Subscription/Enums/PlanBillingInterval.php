<?php

namespace App\Modules\Subscription\Enums;

enum PlanBillingInterval: string
{
    case DAY = 'day';
    case WEEK = 'week';
    case MONTH = 'month';
    case YEAR = 'year';
    case LIFETIME = 'lifetime';
}
