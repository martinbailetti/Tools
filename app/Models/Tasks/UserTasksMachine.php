<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTasksMachine extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['machine', 'user_tasks_id'];
}
