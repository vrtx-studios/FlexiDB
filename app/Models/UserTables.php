<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTables extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'table_name'
    ];



}
