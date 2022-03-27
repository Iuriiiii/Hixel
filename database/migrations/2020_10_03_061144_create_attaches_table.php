<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attaches', function (Blueprint $table) {
            $table->id()->comment('Identificador único del enlazado');
            $table->bigInteger('postid')->comment('El identificador del post que contendrá el enlazado');
            $table->text('url')->comment('La URL del enlazado');
            $table->char('file',150)->default('')->comment('El nombre del archivo, en cazo de ser almacenado');
            $table->tinyInteger('type')->comment('El tipo de enlazado');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attaches');
    }
}
