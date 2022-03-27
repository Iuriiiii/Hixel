<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\User;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $user = User::firstOrCreate([
            'userid' => hash('sha256',$request->ip()),
        ],[
            'nickname' => Str::random(10)
        ]);
        
        return $user;
    }
}
