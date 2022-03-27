<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/testing',function(Request $request){
//     return [Auth::id()];//;
// });

// Route::get('/login',function(Request $request){
//     $user = User::getUser();
//     Auth::login($user);

//     return [Auth::id()];
// })->name('login');
Route::get('/testing',function(Request $request){
    return <<<EOL

<html>
    <head>
        <title>test</title>
    </head>
    <body>
    <iframe
    src="https://player.twitch.tv/?channel=kasttwitch&parent=peridural.top"
    height="<height>"
    width="<width>"
    frameborder="<frameborder>"
    scrolling="<scrolling>"
    allowfullscreen="<allowfullscreen>">
</iframe>
    </body>
</html>
    
EOL;

});

Route::get('/search', [IndexController::class, 'searchPosts'])->name('search');
Route::view('/rules','rules')->name('rules');
Route::get('/{table?}', [IndexController::class, 'index'])->name('board');
Route::get('/{table}/{post}', [IndexController::class, 'readPost'])->name('post_url');
