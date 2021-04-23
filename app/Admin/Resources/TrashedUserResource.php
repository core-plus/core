<?php

namespace Core\Admin\Resources;

use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TrashedUserResource extends JsonResource
{
    /**
     * The resource to array handle.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function toArray($request): array
    {
        // The $request unused.
        unset($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'deleted_at' => Carbon::parse($this->deleted_at)->toIso8601ZuluString(),
        ];
    }
}
