<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('moniker');
            $table->string('alt_moniker')->nullable();
            $table->string('email')->nullable(); // alternate correspondence email
            $table->string('city')->nullable();
            $table->string('territory')->nullable();
            $table->char('country_code', 3)->nullable();
            $table->string('official_url')->nullable();
            $table->string('profile_url');
            $table->unsignedInteger('rank')->nullable();
            $table->unsignedInteger('profilable_id');
            $table->string('profilable_type');
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
        Schema::dropIfExists('profiles');
    }
}
