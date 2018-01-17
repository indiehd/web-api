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
            $table->string('moniker');
            $table->string('alt_moniker')->nullable();
            $table->string('email');
            $table->string('city');
            $table->string('territory');
            $table->char('country_code', 2);
            // $table->foreign('country_code')->references('code')->on('countries');
            $table->string('official_url')->nullable();
            $table->string('profile_url');
            $table->unsignedInteger('rank')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('approver_id')->nullable();
            // $table->foreign('approver_id')->references('id')->on('users');
            $table->unsignedInteger('deleter_id')->nullable();
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
