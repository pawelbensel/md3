<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeForeginKeyToTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prop_prices', function (Blueprint $table) {
            $table->renameColumn('prop_status_id', 'transaction_id');
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
            $table->renameColumn('transaction_id', 'prop_status_id');
        });
    }
}
