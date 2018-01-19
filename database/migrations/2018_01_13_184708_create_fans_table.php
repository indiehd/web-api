<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable(); // alternate correspondence email
            $table->string('address_one')->nullable();
            $table->string('address_two')->nullable();
            $table->string('city')->nullable();
            $table->string('territory')->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('alt_phone')->nullable();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('fans');
    }
}
