<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class ImageHosting extends Model
{
    use HasFactory;
    
    private const PRIVATE_KEY = '6d207e02198a847aa98d0a2a901485a5';
    private const IMAGE_HOSTING_API_URL = 'https://freeimage.host/api/1/upload';
    
    static function enabled()
    {
        return Self::PRIVATE_KEY !== '';
    }
    
    static function hostPrefix()
    {
        return 'fh/';
    }
    
    static function upload($file)
    {
        //return Http::asForm()->post(Self::IMAGE_HOSTING_API_URL, );

        return Http::attach(
            'source', file_get_contents($file), 'photo.jpg'
        )->post(Self::IMAGE_HOSTING_API_URL,[
            'key' => Self::PRIVATE_KEY,
            'action' => 'upload',
            'format' => 'json'
        ]);
    }
}
