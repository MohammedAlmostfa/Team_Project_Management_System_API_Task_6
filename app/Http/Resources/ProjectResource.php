<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
           'name' => $this->name,
           'description' => $this->description,
           'status' => $this->status,
           'created_at' => $this->created_at->toIso8601String(),
           'updated_at' => $this->updated_at->toIso8601String(),
           'lastoftask' => $this->lastoftask ? [
               'id' => $this->lastoftask->id,
               'title' => $this->lastoftask->title,
               'description' => $this->lastoftask->description,
               'status' => $this->lastoftask->status,
               'priority' => $this->lastoftask->priority,
               'user_id' => $this->lastoftask->user_id,
               'project_id' => $this->lastoftask->project_id,
               'due_date' => $this->lastoftask->due_date,
               'created_at' => $this->lastoftask->created_at->toIso8601String(),
               'updated_at' => $this->lastoftask->updated_at->toIso8601String(),
           ] : null,
           'oldoftask' => $this->oldoftask ? [
               'id' => $this->oldoftask->id,
               'title' => $this->oldoftask->title,
               'description' => $this->oldoftask->description,
               'status' => $this->oldoftask->status,
               'priority' => $this->oldoftask->priority,
               'user_id' => $this->oldoftask->user_id,
               'project_id' => $this->oldoftask->project_id,
               'due_date' => $this->oldoftask->due_date,
               'created_at' => $this->oldoftask->created_at->toIso8601String(),
               'updated_at' => $this->oldoftask->updated_at->toIso8601String(),
           ] : null,
        ];
    }

}
