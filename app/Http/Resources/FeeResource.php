<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'amount' => (float) $this->amount,
            'fee_type' => $this->fee_type,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'is_mandatory' => (bool) $this->is_mandatory,
            'description' => $this->description,

            // Relationships
            'class' => $this->classRoom ? [
                'id' => $this->classRoom->id,
                'name' => $this->classRoom->name,
            ] : null,
            'term' => $this->term ? [
                'id' => $this->term->id,
                'name' => $this->term->name,
            ] : null,
            'session' => $this->session ? [
                'id' => $this->session->id,
                'name' => $this->session->name,
            ] : null,
        ];
    }
}
