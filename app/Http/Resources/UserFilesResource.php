<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFilesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'file_id' => $this->file_id,
            'name' => $this->name,
            'url' => route('get-file', ['file' => $this->file_id]),
            'accesses' => UserFileAccessesResource::collection($this->accesses)
        ];
    }
}
