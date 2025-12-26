<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeePaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount_paid' => (float) $this->amount_paid,
            'payment_date' => $this->payment_date?->format('Y-m-d H:i:s'),
            'payment_method' => $this->payment_method,
            'reference_number' => $this->reference_number,
            'notes' => $this->notes,

            // Relationships
            'student' => $this->student ? [
                'id' => $this->student->id,
                'full_name' => $this->student->full_name,
            ] : null,
            'fee' => $this->fee ? [
                'id' => $this->fee->id,
                'title' => $this->fee->title,
            ] : null,
            'processed_by' => $this->processedBy ? [
                'name' => $this->processedBy->name,
            ] : null,
        ];
    }
}
