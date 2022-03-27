<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Response;
use App\Models\ImageCompressor;

class ImageStoreController extends Controller
{

    public function imgUploadUrl(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'url' => 'required|string|filled|max:150'
        ],[
            'url.required' => 'Se require una URL de la imagen.',
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return['success' => false,'description' => $message];
            }
        }
        
        return ImageCompressor::compressFromUrl($request->url);
    }
    
    public function imgUploadFile(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image' => 'required|file|mimes:jpeg,bmp,png,webp,gif'
        ],[
            'image.required' => 'Se require una imagen.',
        ]);
        
        if($validator->fails())
        {
            foreach($validator->errors()->all() as $message)
            {
                return['success' => false,'description' => $message];
            }
        }
        
        return ImageCompressor::compress($request->image);
    }
}
