<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ImageStoreController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
    // return $request->user();
// });


Route::post('/markpost',[ApiController::class,'markPost'])->name('mark.post');
Route::post('/hidecategory',[ApiController::class, 'toggleCategory'])->name('hide.category');

//Route::get('/testimageapi', [ApiController::class, 'testImgUpload']);
Route::post('/delnotification', [ApiController::class, 'removeNotification'])->name('remove_notification');
//Route::get('getPosts', [ApiController::class, 'getPosts']);
Route::post('/getposts', [ApiController::class, 'getPosts'])->name('get_posts');
Route::get('/getposts', [ApiController::class, 'getPosts']);


//Route::post('/postsubmit', [ApiController::class, 'submitPost'])->name('post_submit');

Route::middleware(['throttle:commentlimit'])->group(function () {
    Route::post('/commentsubmit', [ApiController::class, 'submitComment'])->name('comment_submit');
});

Route::middleware(['throttle:postlimit'])->group(function () {
    Route::post('/postsubmit', [ApiController::class, 'submitPost'])->name('post_submit');
});

Route::get('/postsubmit', [ApiController::class, 'submitPost'])->name('getpost_submit');

//Route::get('/commentsubmit', [ApiController::class, 'submitComment']);
Route::post('/imageurlup', [ImageStoreController::class, 'imgUpload']);
Route::post('/imagefileup', [ImageStoreController::class, 'imgUploadFile']);
Route::post('/postaction',[ApiController::class,'postAction'])->name('post_action');
Route::post('/preview', [ApiController::class, 'preview'])->name('post_preview');
Route::get('/showpreview', [ApiController::class, 'showPreview'])->name('show_preview');
Route::post('/changetheme', [ApiController::class, 'changeTheme']);
Route::post('/getlastcomment',[ApiController::class, 'getLastComment']);
//Route::get('/test', [ImageStoreController::class, 'test']);