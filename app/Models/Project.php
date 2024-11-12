<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'description',
        'status',
    ];


    public function getStatusAttribute($status)
    {
        return $status ? 'A team has been appointed' : 'No team has been assigned';
    }

    // relation with user
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withPivot('last_activity');
    }
    // relation with task
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    // get las task
    public function lastoftask()
    {
        return $this->hasone(Task::class)->latestOfMany();
    }
    // get las task
    public function oldoftask()
    {
        return $this->hasone(Task::class)->oldestOfMany();
    }
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }


}
