<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->foreign('flac_file_id', 'flac_file_id_fk')->references('id')->on('flac_files');
            // TODO Uncomment these once the referenced objects exist.
            #$table->foreign('catalog_id')->references('id')->on('catalogs');
            #$table->foreign('sku_id')->references('id')->on('skus');
        });

        Schema::table('album_genre', function (Blueprint $table) {
            $table->foreign('album_id', 'album_id_fk')->references('id')->on('albums');
            $table->foreign('genre_id', 'genre_id_fk')->references('id')->on('genres');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->foreign('deleter_id', 'deleter_id_fk')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
