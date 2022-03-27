<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codes', function (Blueprint $table) {
            $table->id();
            $table->char('code',20)->comment('El código');
            $table->tinyInteger('type')->comment('El tipo de código');
            $table->tinyInteger('uses')->default(1)->comment('La cantidad de usos máximos');
            $table->bigInteger('extra')->default(null)->nullable()->comment('Un argumento extra');
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
        Schema::dropIfExists('codes');
    }
}
