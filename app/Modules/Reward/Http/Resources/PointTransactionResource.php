<?php

namespace App\Modules\Reward\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'amount' => (int) $this->amount,
            'type' => $this->type,
            'before_balance' => (int) $this->before_balance,
            'after_balance' => (int) $this->after_balance,
            'description' => $this->description,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
