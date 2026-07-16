<?php
namespace App\Enums;

enum Verdict: string {
    case RED = 'red';
    case OJASIS_APPROVED = 'ojais_approved';
    case NEUTRAL = 'neutral';
}
