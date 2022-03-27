<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id()                                                                       ->comment('Identificador único del post');
            $table->text('sid')                                                                ->comment('Identificador de cadena para el post');
            $table->bigInteger('userid')                                                       ->comment('Identificador único del usuario creador');
            $table->bigInteger('postid')->default(null)->nullable()                                           ->comment('Identificador del post padre');
            //$table->bigInteger('postid2')->default(null)->nullable()                                           ->comment('Identificador del comentario comentado');
            $table->bigInteger('categoryid')->nullable()                                                     ->comment('Identificador de la categoría');
            //$table->bigInteger('attachid')->default(0)                                         ->comment('El ID del archivo o URL enlazado');
            $table->bigInteger('audioid')->nullable()                                                     ->comment('Identificador del audio');
            $table->bigInteger('front')->default(1)->nullable()->comment('Identificador de la imagen de presentación');
            //$table->char('front',255)->default(null)->nullable()->comment('La portada de la publicación');
            $table->char('title', 100)->default('')->nullable()                                            ->comment('El título del post');
            $table->text('content')->default(null)->nullable()                                                            ->comment('El contenido del post');
            $table->bigInteger('likes')->default(0)                                            ->comment('La cantidad de "me gustas"');
            $table->bigInteger('unlikes')->default(0)                                          ->comment('La cantidad de "no me gusta"');
            $table->dateTime('last_update')->default(null)->nullable();
            //$table->dateTime('creation_date', 0)->default(new Expression('CURRENT_TIMESTAMP')) ->comment('El tiempo de creación');
            $table->tinyInteger('status')->default(0)                                              ->comment('El estado del post');
            $table->tinyInteger('extra')->default(null)->nullable()->comment('Un argumento extra');
            $table->tinyInteger('rps')->default(null)->nullable()->comment('Valor del piedra, papél o tijeras');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
