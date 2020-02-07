<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationsToStatues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prop_statuses', function (Blueprint $table) {
            $table->unsignedBigInteger('price_id');
            $table->foreign('price_id')->references('id')->on('prop_prices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statues', function (Blueprint $table) {
            $table->dropColumn('price_id');
        });
    }
}
