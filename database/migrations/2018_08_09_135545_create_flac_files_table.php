<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlacFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flac_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('file_size');
            $table->string('file_format');
            $table->string('data_format');
            $table->string('bitrate_mode');
            $table->boolean('is_lossless');
            $table->unsignedTinyInteger('num_channels');
            $table->unsignedInteger('sample_rate');
            $table->unsignedTinyInteger('bits_per_sample');
            $table->unsignedDecimal('bitrate', 15, 7);
            $table->string('encoder');
            $table->string('channel_mode');
            $table->unsignedDecimal('compression_ratio', 15, 14);
            $table->string('encoding');
            $table->string('mime_type');
            $table->unsignedDecimal('play_time_seconds', 11, 7);
            $table->string('md5_data_source', 32);
            $table->string('sha256', 64);
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
        Schema::dropIfExists('flac_files');
    }
}
