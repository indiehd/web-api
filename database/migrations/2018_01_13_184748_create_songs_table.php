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
            $table->unsignedInteger('flac_file_id');
            $table->unsignedTinyInteger('track_number')->default(1);
            $table->unsignedDecimal('preview_start', 7, 3)->default(0.000);
            $table->boolean('is_active')->default(true);
            // TODO Uncomment these once the back-catalog is implemented.
            #$table->boolean('is_in_back_catalog')->default(false);
            #$table->unsignedInteger('catalog_id');
            $table->unsignedInteger('sku_id');
            $table->unsignedInteger('album_id');
            $table->unique(['album_id', 'track_number'], 'unique_track_per_album');
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
