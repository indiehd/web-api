<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('alt_name')->nullable();
            $table->unsignedTinyInteger('track_number')->default(1);
            $table->float('preview_start')->default(0.00);
            $table->boolean('is_active')->default(true);
            //$table->boolean('is_in_back_catalog')->default(false);
            //$table->unsignedInteger('catalog_id');
            //$table->foreign('catalog_id')->references('id')->on('catalogs');
            $table->unsignedInteger('sku_id');
            $table->foreign('sku_id')->references('id')->on('skus');
            $table->unsignedInteger('album_id');
            $table->foreign('album_id')->references('id')->on('albums');
            $table->unique(['album_id', 'track_number']);
            $table->softDeletes();
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
        Schema::dropIfExists('songs');
    }
}
