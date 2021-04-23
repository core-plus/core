<?php

namespace Core\API2\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Ability extends JsonResource
{
    /**
     * The resource to array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
        ];
    }
}
