<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'total_amount' => $this->total_amount,
            'confirmed_at' => optional($this->confirmed_at)->toISOString(),
            'shipped_at' => optional($this->shipped_at)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
