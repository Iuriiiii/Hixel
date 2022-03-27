<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Front;
use App\Models\GifFrameExtractor;
use Illuminate\Support\Facades\Log;

class ImageCompressor extends Model
{
    use HasFactory;

    const IMAGE_SIZE = 150;

    private static function isWebpAnimated($src)
    {
        return strpos(file_get_contents($src),"ANMF") !== false;
    }

    private static function saveThumbnail(&$info,&$i,$d,$w = 32,$h = 32,$q = 10)
    {
        if($i === false) return;
        imagecopyresized($thumbnail = imagecreatetruecolor($w,$h),$i,0,0,0,0,$w,$h,$info[0],$info[1]);
        @imagewebp($thumbnail,$d,$q);
        imagedestroy($thumbnail);
    }
    
    private static function savePreview($info,$image,$destiny,$quality = 50)
    {
        if($image === false) return;
        $thumbnail = imagecreatetruecolor($info[0],$info[1]);
        imagecopyresized($thumbnail,$image,0,0,0,0,$info[0],$info[1],$info[0],$info[1]);
        @imagewebp($thumbnail,$destiny,$quality);
        imagedestroy($thumbnail);
    }

    private static function imgUpload(&$imgname,$destination)
    {
        if(ImageHosting::enabled())
        {
            $imghosting = ImageHosting::upload($destination);
            
            if($imghosting['status_code'] == 200)
                $imgname = ImageHosting::hostPrefix() . $imghosting['image']['image']['name'];
            else
                Log::error('Status_code !== 200');
        }
    }
    
    public static function saveWithoutCompress($source,$quality = 10) {
        $hash = hash_file('sha256',$source);
        $imgname = $hash;
        
        if(intval(@filesize($source)) > 4000000)
            return ['success' => false,'description' => 'Tamaño permitido excedido.'];
        
        if(($info = @getimagesize($source)) === false)
            return ['success' => false];
        
        if ($info['mime'] == 'image/jpeg')
            $imgname .= '.jpeg';

        elseif ($info['mime'] == 'image/gif')
            $imgname .= '.gif';
            
        elseif ($info['mime'] == 'image/png')
            $imgname .= '.png';
        
        elseif ($info['mime'] == 'image/bmp')
            $imgname .= '.bmp';
        
        elseif ($info['mime'] == 'image/webp')
            $imgname .= '.webp';
        
        $destination = public_path('img') . '/' . $imgname;
        
        if(file_exists($destination))
            return ['success' => true,'url' => $imgname];

        move_uploaded_file($source,$destination);

        return ['success' => true,'url' => $imgname];
    }
    
    public static function compress($source,$quality = 30)
    {
        $animated = false;
        $hash = hash_file('sha256',$source);
        $imgname = $hash . '.webp';
        $destination_l = public_path('img') . '/l/' . $imgname;
        $destination_t = public_path('img') . '/t/' . $imgname;
        $destination = public_path('img') . '/' . $imgname;
        $hashs = ['original' => $hash];
        $imgsize = Self::IMAGE_SIZE;
        $wraw = "original_sha256 = '{$hash}' or path_sha256 = '{$hash}' or preview_sha256 = '{$hash}' or thumbnail_sha256 = '{$hash}'";
        $w = ['original_sha256' => $hash,'path_sha256' => $hash,'preview_sha256' => $hash,'thumbnail_sha256' => $hash,'status' => Front::STATUS_BANNED];
        // //if(file_exists($destination))
        // //    return ['success' => true,'exists' => true,'url' => $imgname,'destination' => '/public/img/' . $imgname,'destination_l' => '/public/img/l/' . $imgname,'destination_t' => '/public/img/t/' . $imgname];
        
        // if(($front = Front::whereRaw("{$wraw} and type = " . Front::TYPE_BANNED))->exists())
        //     return ['success' => false,'exists' => false,'description' => 'Esta imagen está baneada.'];

        // if(($front = Front::whereRaw("{$wraw} and type = " . Front::TYPE_IMAGE))->exists())
        //     return ['success' => true,'animated' => $animated,'exists' => true,'id' => $front->first()->id];

        if(($front = Front::where($w)->exists()))
            return ['success' => false,'exists' => false,'description' => 'Esta imagen está baneada.'];

        $w['type'] = Front::TYPE_IMAGE;

        if(($front = Front::where($w)->exists()))
            return ['success' => true,'animated' => $animated,'exists' => true,'id' => $front->first()->id];

        if(($info = @getimagesize($source)) === false)
            return ['success' => false,'description' => 'Se produjo un error al obtener dimensiones de la imagen.s'];

        if(strpos($info['mime'],'image/') === false)
            return ['success' => false,'description' => 'Se esperaba una imagen.'];

        if(intval(@filesize($source)) > 5000000)
            return ['success' => false,'description' => 'Tamaño permitido excedido.'];

        do
        {
            if ($info['mime'] == 'image/jpeg')
                $image = @imagecreatefromjpeg($source);

            elseif ($info['mime'] == 'image/gif')
            {
                $animated = GifFrameExtractor::isAnimatedGif($source);

                $imgname = $hash . '.gif';
                $destination =   public_path('img') . '/' . $imgname;

                $image = @imagecreatefromgif($source);
                self::saveThumbnail($info,$image,$destination_t);
                self::savePreview($info,$image,$destination_l);
                imagedestroy($image);
                $source->move(public_path('img'),$imgname);
                
                $hashs['thumbnail'] = hash_file('sha256',$destination_t);
                $hashs['preview'] = hash_file('sha256',$destination_l);
                $hashs['path'] = $hash;

                return ['success' => true,'animated' => $animated,'url' => $imgname,'exists' => false,'destination' => '/public/img/' . $imgname,'destination_l' => '/public/img/l/' . $hash . '.webp','destination_t' => '/public/img/t/' . $hash . '.webp','hashs' => $hashs];
            }
            elseif ($info['mime'] == 'image/png')
                $image = @imagecreatefrompng($source);
            
            elseif ($info['mime'] == 'image/bmp')
                $image = @imagecreatefrombmp($source);
            
            elseif ($info['mime'] == 'image/webp')
            {
                if(!$animated = self::isWebpAnimated($source))
                    if($image = @imagecreatefromwebp($source))
                        break;

                $cwebp = 'cwebp.exe';

                shell_exec("\"{$cwebp}\" -quiet -mt -q {$quality} -lossless -m 6 -resize {$imgsize} {$imgsize} \"{$source}\" -o \"{$destination_l}\"");
                shell_exec("\"{$cwebp}\" -quiet -mt -q 10 -lossless -m 6 -resize 32 32 \"{$source}\" -o \"{$destination_t}\"");
                $source->move(public_path('img'),$imgname);

                $hashs['thumbnail'] = hash_file('sha256',$destination_t);
                $hashs['preview'] = hash_file('sha256',$destination_l);
                $hashs['path'] = $hash;

                return ['success' => true,'animated' => $animated,'url' => $imgname,'exists' => false,'destination' => '/public/img/' . $imgname,'destination_l' => '/public/img/l/' . $hash . '.webp','destination_t' => '/public/img/t/' . $hash . '.webp','hashs' => $hashs];
            }
        } while(0);
        
        self::saveThumbnail($info,$image,$destination_t);
        self::savePreview($info,$image,$destination_l);
        imagewebp($image,$destination,100);
        imagedestroy($image);
        
        $hashs['thumbnail'] = hash_file('sha256',$destination_t);
        $hashs['preview'] = hash_file('sha256',$destination_l);
        $hashs['path'] = hash_file('sha256',$destination);

        return ['success' => true,'animated' => $animated,'url' => $imgname,'exists' => false,'destination' => '/public/img/' . $imgname,'destination_l' => '/public/img/l/' . $imgname,'destination_t' => '/public/img/t/' . $imgname,'hashs' => $hashs];
    }
    
    public static function compressFromUrl($url, $quality = 10)
    {
        if(($headers = get_headers($url,1)) === false)
        {
            return ['sucess' => false,'description' => 'Se produjo un error al solicitar información de la imagen.'];
        }
        
        if($headers['Content-Length'] > 1024*600)
        {
            return ['success' => false,'description' => 'La imagen es demaciado pesada.'];
        }
        
        switch($headers['Content-Type'])
        {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($url);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($url);
                break;
            case 'image/png':
                $image = imagecreatefrompng($url);
                break;
            case 'image/bmp':
                $image = imagecreatefrombmp($url);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($url);
                break;
            default:
                return ['success' => false,'description' => 'Formato inválido.'];
        }
        
        $destination = resource_path() . '/public/img/' . Str::random(15) . '.webp';;
        
        imagewebp($image,$destination,$quality);
        
        return ['success' => true,'url' => url($destination)];
    }
}
