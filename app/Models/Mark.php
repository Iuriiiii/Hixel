<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    public const TYPE_HIDDED = 1;
    public const TYPE_FAVORITE = 2;
    public const TYPE_REPORTED = 3;
}
