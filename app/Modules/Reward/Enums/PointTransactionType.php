<?php

namespace App\Modules\Reward\Enums;

enum PointTransactionType: string
{
    case EARNED = 'earned';
    case SPENT = 'spent';
    case REDEEMED = 'redeemed';
    case EXPIRED = 'expired';
    case ADJUSTED = 'adjusted';
}
