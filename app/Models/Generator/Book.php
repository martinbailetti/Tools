<?php

namespace App\Models\Generator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $primaryKey = 'token';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['token', 'config'];
}
