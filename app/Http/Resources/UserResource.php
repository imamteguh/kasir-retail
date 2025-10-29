<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $store = $this->stores->first();
        $planName = $store?->currentSubscription?->plan?->name;
        $subscriptionStatus = $store?->currentSubscription?->status;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->avatar,
            'plan' => $planName,
            'subscription_status' => $subscriptionStatus,
            'created_at' => $this->created_at->format('d-m-Y H:i:s'),
        ];
    }
}
