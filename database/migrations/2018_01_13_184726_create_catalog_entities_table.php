<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_entities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            $table->string('title')->nullable();
            $table->string('email')->nullable(); // alternate correspondence email
            $table->string('address_one');
            $table->string('address_two')->nullable();
            $table->string('city');
            $table->string('territory');
            $table->char('country_code', 3);
            $table->string('postal_code');
            $table->string('phone');
            $table->string('alt_phone')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('approver_id')->nullable();
            // $table->foreign('approver_id')->references('id')->on('users');
            $table->unsignedBigInteger('deleter_id')->nullable();
            // $table->foreign('deleter_id')->references('id')->on('users');
            $table->unsignedInteger('catalogable_id');
            $table->string('catalogable_type');
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
        Schema::dropIfExists('catalog_entities');
    }
}
