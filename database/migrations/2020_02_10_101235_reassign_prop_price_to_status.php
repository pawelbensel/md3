<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReassignPropPriceToStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prop_prices', function (Blueprint $table) {
            $table->dropForeign('prop_prices_prop_id_foreign');
            $table->dropColumn('prop_id');
            $table->unsignedBigInteger('prop_status_id');
            $table->foreign('prop_status_id')->references('id')->on('prop_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prop_prices', function (Blueprint $table) {
            $table->dropColumn('prop_status_id');
            $table->unsignedBigInteger('prop_id');
            $table->foreign('prop_id')->references('id')->on('props');
        });
    }
}
