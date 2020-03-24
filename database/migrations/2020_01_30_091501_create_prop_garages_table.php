<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropGaragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prop_garages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prop_id');
            $table->string('garage');
            $table->timestamps();
            $table->foreign('prop_id')->references('id')->on('props');
            $table->index('garage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prop_garages');
    }
}
