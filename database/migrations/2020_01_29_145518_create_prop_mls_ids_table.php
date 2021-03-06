<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropMlsIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prop_mls_ids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prop_id');
            $table->string('mls_id');
            $table->string('mls_name');
            $table->timestamps();
            $table->foreign('prop_id')->references('id')->on('props');
            $table->index('mls_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prop_mls_ids');
    }
}
