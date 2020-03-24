<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeProp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_prop', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('office_id')->unsigned();
            $table->integer('prop_id')->unsigned();
            $table->timestamps();
            $table->index('office_id');
            $table->index('prop_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_prop');
    }
}
