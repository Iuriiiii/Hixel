<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Mark;

class CreateMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('userid')->comment('Usuario que escondió una publicación o categoría');
            $table->bigInteger('categoryid')->nullable()->default(null)->comment('La categoría a esconder');
            $table->bigInteger('postid')->nullable()->default(null)->comment('La publicación a esconder');
            $table->bigInteger('type')->nullable()->default(Mark::TYPE_HIDDED)->comment('El tipo de acción realizada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marks');
    }
}
