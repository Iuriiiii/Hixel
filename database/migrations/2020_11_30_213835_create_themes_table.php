<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Theme;

class CreateThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->char('name',32)->comment('El nombre del tema');
            $table->char('path')->comment('La dirección del tema');
            //$table->timestamps();
        });
        
        $themes = [
            ['Purple',url('/public/css/purple.css')],
            ['Gray',url('/public/css/gray.css')],
            ['Night',url('/public/css/night.css')],
            ['Day',url('/public/css/day.css')]
        ];
        
        foreach($themes as $item)
        {
            $theme = new Theme;
            $theme->name = $item[0];
            $theme->path = $item[1];
            $theme->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('themes');
    }
}
