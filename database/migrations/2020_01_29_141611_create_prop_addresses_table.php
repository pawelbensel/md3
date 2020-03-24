<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prop_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prop_id');
            $table->string('street_unit')->nullable();
            $table->string('street_suffix')->nullable();
            $table->string('street_post_direction')->nullable();
            $table->string('street_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('street_direction')->nullable();
            $table->string('county')->nullable();
            $table->string('city')->nullable();
            $table->timestamps();
            $table->foreign('prop_id')->references('id')->on('props');
            $table->index('city');
            $table->index('street_name');
            $table->index(['street_name','city']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prop_addresses');
    }
}
