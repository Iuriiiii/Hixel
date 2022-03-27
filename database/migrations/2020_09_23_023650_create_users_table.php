<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('Identificador único del usuario');
            $table->char('address',15)->comment('(IP) Identificador único del usuario creador');
            $table->char('nickname',10)->comment('El nombre de usuario del anonimo');
            $table->char('sid',64)->comment('Identificador único');
            $table->tinyInteger('status')->default(0)->nullable()->comment('El tipo de usuario o su estado.');
            $table->dateTime('unban_date')->default(null)->nullable()->comment('Identificador único del usuario creador');
            $table->tinyInteger('theme')->default(1)->comment('El tema que el usuario usará en la página.');
            $table->char('password',60)->default(null)->nullable()->comment('La contraseña de la cuenta');
            $table->char('remember_token',100)->default(null)->nullable()->comment('El toquen de recordado');
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
        Schema::dropIfExists('users');
    }
}
