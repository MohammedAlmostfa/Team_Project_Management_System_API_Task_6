<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
'title',
'description',
'status',
'priority',
'user_id',
'project_id',
'due_date'
    ];
    //rlation wth user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    //rotation wth project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    // filter by status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    //filter by priority
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopePrioritytask($query, $title)
    {
        return $query->orderByRaw("FIELD(priority, 'high', 'middle', 'low')")->where('title', 'like', '%' . $title . '%');
    }
}
