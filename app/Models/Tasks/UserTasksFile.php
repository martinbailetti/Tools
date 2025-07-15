<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTasksFile extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['user_tasks_id', 'path', 'name'];
}
