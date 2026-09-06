<?php

namespace App\Modules\Payment\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';
    case PAYMENT = 'payment';
    case REFUND = 'refund';
    case TRANSFER_IN = 'transfer_in';
    case TRANSFER_OUT = 'transfer_out';
    case FEE = 'fee';
    case ADJUSTMENT = 'adjustment';
}
