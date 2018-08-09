<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->increments('id');
            #$table->unsignedInteger('profile_id');
            #$table->foreign('profile_id')->references('id')->on('artists');
            $table->string('title');
            $table->string('alt_title')->nullable();
            $table->unsignedSmallInteger('year');
            $table->string('description')->nullable();
            $table->boolean('has_explicit_lyrics');
            $table->unsignedDecimal('full_album_price', 8, 4)->nullable();
            $table->unsignedInteger('rank')->nullable();
            $table->boolean('is_active')->default(false);
            $table->unsignedInteger('deleter_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('albums');
    }
}
