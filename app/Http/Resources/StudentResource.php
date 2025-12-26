<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'admission_number' => $this->admission_number,
            'admission_date' => $this->admission_date?->format('Y-m-d'),
            'status' => (bool) $this->status,
            'blood_group' => $this->blood_group,
            'emergency_contact' => $this->emergency_contact,
            'medical_conditions' => $this->medical_conditions,
            'full_name' => $this->full_name,
            'current_class' => $this->current_class,

            // Relationships
            'user' => [
                'name' => $this->user?->name,
                'email' => $this->user?->email,
                'gender' => $this->user?->gender,
            ],
            'class_room' => $this->classRoom ? [
                'id' => $this->classRoom->id,
                'name' => $this->classRoom->name,
            ] : null,
            'section' => $this->section ? [
                'id' => $this->section->id,
                'name' => $this->section->name,
            ] : null,
        ];
    }
}
