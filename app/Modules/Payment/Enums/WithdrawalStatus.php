<?php

namespace App\Modules\Payment\Enums;

enum WithdrawalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELED = 'canceled';
    case CANCELLED = 'cancelled';
}
