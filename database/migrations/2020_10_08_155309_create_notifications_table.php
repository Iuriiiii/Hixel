<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('userid')->comment('El identificador del usuario que recibirá la notificación');
            $table->bigInteger('postid')->comment('El identificador del post que recibió la notificación');
            $table->bigInteger('commentid')->comment('El identificador del comentario que realizó la notificación');
            $table->bigInteger('commentidowner')->default(null)->nullable()->comment('Identificador del post padre.');
            $table->bigInteger('type')->comment('El tipo de notificación');
            $table->bigInteger('counter')->default(1);
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
        Schema::dropIfExists('notifications');
    }
}
