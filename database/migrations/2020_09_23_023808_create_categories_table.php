<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la categoría');
            $table->text('identifier')->comment('Identificador de la categoría');
            $table->char('diminutive',10)->comment('Diminutivo de la categoría');
            $table->bigInteger('status')->default(0)->comment('Tipo de categoría');
            $table->bigInteger('access')->default(Category::ACCESS_PUBLIC)->comment('Tipo de acceso');
        });
        
        $categories = [
            ['General','g'],
            ['Historias','his'],
            ['Política','pol'],
            ['NSFW','xxx',Category::STATUS_NSFW],
            ['Anime','anm'],
            ['Cine','flm'],
            ['Programación','prg'],
            ['Paranormal','x'],
            ['Redes','red'],
            ['Noticias','ntc'],
            ['Arte y Dibujo','ayd'],
            ['Música','mus'],
            ['Favoritos','fav',Category::STATUS_FAVORITES],
            ['Mis Publicaciones','mpb',Category::STATUS_MY],
            ['Escondidos','hdn',Category::STATUS_HIDDED],
            ['Basurero','trsh',Category::STATUS_TRASH,Category::ACCESS_STAFF],
            ['Reportes','rep',Category::STATUS_REPORTS,Category::ACCESS_STAFF]
        ];
        
        foreach($categories as $item)
        {
            $category = new Category;
            $category->identifier = $item[0];
            $category->diminutive = $item[1];
            $category->status = $item[2] ?? Category::STATUS_PUBLIC;
            $category->access = $item[3] ?? Category::ACCESS_PUBLIC;
            $category->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
