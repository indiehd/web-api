<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            //$table->foreign('catalog_id')->references('id')->on('catalogs');
        });

        Schema::table('genres', function (Blueprint $table) {
            $table->foreign('approver_id', 'approver_id_fk')->references('id')->on('users');
        });

        Schema::table('album_genre', function (Blueprint $table) {
            $table->foreign('album_id', 'album_id_fk')->references('id')->on('albums');
            $table->foreign('genre_id', 'genre_id_fk')->references('id')->on('genres');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->foreign('artist_id', 'artist_id_fk')->references('id')->on('artists');
            $table->foreign('deleter_id', 'deleter_id_fk')->references('id')->on('users');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign('user_id', 'user_id_fk')->references('id')->on('users');
            $table->foreign('country_code', 'accounts_country_code_fk')->references('code')->on('countries');
        });

        Schema::table('catalog_entities', function (Blueprint $table) {
            $table->foreign('country_code', 'catalog_entities_country_code_fk')->references('code')->on('countries');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->foreign('country_code', 'profiles_country_code_fk')->references('code')->on('countries');
        });

        Schema::table('digital_assets', function (Blueprint $table) {
            $table->foreign('product_id', 'product_id_fk')->references('id')->on('products');
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
