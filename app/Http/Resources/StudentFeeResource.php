<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentFeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'balance' => (float) $this->balance,
            'status' => $this->status,

            // Relationships
            'fee' => new FeeResource($this->whenLoaded('fee')),
            'student' => [
                'id' => $this->student_id,
                'full_name' => $this->student?->full_name,
            ],
        ];
    }
}
