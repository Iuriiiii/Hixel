<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Front;

class CreateFrontsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fronts', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('animated')->default(0)->comment('¿La imagen es animada? 1 si, 0 no');
            $table->char('path')->comment('Dirección de la imagen');
            $table->char('preview')->comment('Dirección de la imagen');
            $table->char('thumbnail')->comment('Dirección de la imagen');
            $table->char('original_sha256',64)->comment('El hash de la imagen original');
            $table->char('path_sha256',64)->comment('El hash de la imagen en nuestro almacen');
            $table->char('preview_sha256',64)->comment('El hash del preview en nuestro almacen');
            $table->char('thumbnail_sha256',64)->comment('El hash del thumbnail en nuestro almacen');
            $table->char('externpath')->nullable()->default(null)->comment('Dirección de la imagen');
            $table->char('externpreview')->nullable()->default(null)->comment('Dirección de la imagen');
            $table->char('externthumbnail')->nullable()->default(null)->comment('Dirección de la imagen');
            $table->char('extra')->nullable()->default(null)->comment('Algún dato extra');
            $table->tinyInteger('type')->default(Front::TYPE_IMAGE)->comment('El tipo de frente');
            $table->tinyInteger('status')->default(Front::STATUS_NORMAL)->comment('El estado de la imagen');
            $table->timestamps();
        });
        
        $front = new Front;
        $front->path = url('/public/img/default.webp');
        $front->preview = url('/public/img/l/default.webp');
        $front->thumbnail = url('/public/img/t/default.webp');
        $front->original_sha256 = hash_file('sha256',public_path('img') . '/default.webp');
        $front->path_sha256 = hash_file('sha256',public_path('img') . '/default.webp');
        $front->preview_sha256 = hash_file('sha256',public_path('img') . '/l/default.webp');
        $front->thumbnail_sha256 = hash_file('sha256',public_path('img') . '/t/default.webp');
        $front->type = Front::TYPE_IMAGE;
        $front->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fronts');
    }
}
