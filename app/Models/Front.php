<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ImageCompressor;
use Illuminate\Support\Facades\File;

class Front extends Model
{
    use HasFactory;
    
    // public const TYPE_IMAGE = 1;
    // public const TYPE_YOUTUBE_VIDEO = 3;
    // public const TYPE_DAYLIMOTION_VIDEO = 4;
    // public const TYPE_EXTERN_IMAGE = 5;
    // public const TYPE_BANNED = 9;
    // public const TYPE_IMAGE_CENSORED = 2;

    public const TYPE_IMAGE = 0b00000001;
    public const TYPE_YOUTUBE_VIDEO = 0b00000010;
    public const TYPE_DAYLIMOTION_VIDEO = 0b00000100;
    public const TYPE_EXTERN_IMAGE = 0b00100000;

    public const STATUS_NORMAL = 0;
    public const STATUS_BANNED = 2;
    public const STATUS_CENSORED = 1;
    
    public function deleteWithImage()
    {
        $paths = str_replace(env('APP_URL'),'',[$this->path,$this->preview,$this->thumbnail]);
        File::delete($paths[0]);
        File::delete($paths[1]);
        File::delete($paths[2]);
        $this->delete();
    }

    public function banMe()
    {
        $paths = str_replace(env('APP_URL'),'',[$this->path,$this->preview,$this->thumbnail]);
        File::delete($paths[0]);
        File::delete($paths[1]);
        File::delete($paths[2]);
        $this->status = Self::STATUS_BANNED;
        $this->save();
    }

    // Devuelve el ID de la imagen.
    static function saveImage(&$image,&$error = null,$censore = false,&$url = null)
    {
        $compressed = ImageCompressor::compress($image);
        
        if(!$compressed['success'])
        {
            $error = $compressed['description'];
            return 0;
        }
        
        if(!$compressed['exists'])
        {
            return Front::insertGetId([
                'animated' => $compressed['animated'],
                'path' => url($compressed['destination']),
                'preview' => url($compressed['destination_l']),
                'thumbnail' => url($compressed['destination_t']),
                'original_sha256' => $compressed['hashs']['original'],
                'path_sha256' => $compressed['hashs']['path'],
                'preview_sha256' => $compressed['hashs']['preview'],
                'thumbnail_sha256' => $compressed['hashs']['thumbnail'],
                'type' => Front::TYPE_IMAGE,
                'status' => $censore ? Front::STATUS_CENSORED : 0,
            ]);
        }

        return $compressed['id'];
    }
}
