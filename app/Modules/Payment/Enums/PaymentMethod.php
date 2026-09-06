<?php

namespace App\Modules\Payment\Enums;

enum PaymentMethod: string
{
    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';
    case SSLCOMMERZ = 'sslcommerz';
    case BKASH = 'bkash';
    case WALLET = 'wallet';
    case COD = 'cod';
    case BANK_TRANSFER = 'bank_transfer';
}
