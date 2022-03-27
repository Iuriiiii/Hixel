<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class ReCaptcha extends Model
{
    use HasFactory;
    
    private const PUBLIC_KEY = '6LdhYuIZAAAAAE29l4UUVcz5MvnyntHviotuvlwa';
    private const PRIVATE_KEY = '6LdhYuIZAAAAAN-WipN47R89D5Ie7D0PEPmvznZb';
    private const RE_CAPTCHA_API_URL = 'https://www.google.com/recaptcha/api/siteverify';
    
    public static function hasReCaptcha()
    {
        return false;//(Self::PUBLIC_KEY !== '') && (Self::PRIVATE_KEY !== '');
    }
    
    public static function isValidResponse($response)
    {
        return Http::asForm()->post(Self::RE_CAPTCHA_API_URL, [
            'secret' => Self::PRIVATE_KEY,
            'response' => $response
        ])->json();
    }
    
}
