<?php

namespace Ngodingskuyy\Doctypes\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DoctypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'description' => $this->description,
            'fields' => $this->generateFormSchema(),
            'settings' => $this->settings,
            'is_active' => $this->is_active,
            'is_system' => $this->is_system,
            'icon' => $this->icon,
            'color' => $this->color,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'fields_count' => $this->doctypeFields->count(),
        ];
    }
}
