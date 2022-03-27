<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    public const STATUS_PUBLIC = 0;
    public const STATUS_NSFW = 1;
    public const STATUS_MY = 2;
    public const STATUS_FAVORITES = 3;
    public const STATUS_HIDDED = 4;
    public const STATUS_TRASH = 5;
    public const STATUS_REPORTS = 6;

    public const ACCESS_PUBLIC = 0;
    public const ACCESS_STAFF = 1; // Administrador y moderadores
    public const ACCESS_ADMINISTRATOR = 2;

    protected $fillable = ['identifier','diminutive','nsfw'];
    public $timestamps = false;
    
    public function isIdNsfw($id)
    {
        return DB::table('categories')->find($id)->nsfw === 1;
    }
    
    public function isNsfw()
    {
        return $this->status === Self::STATUS_NSFW;
    }

    public function getAllAsJson()
    {
        return $this->all()->toJson();
    }
}
