<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropPropSoldPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('prop_sold_prices');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('prop_sold_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('prop_id');
            $table->string('sold_price');
            $table->timestamps();
            $table->foreign('prop_id')->references('id')->on('props');
            $table->index('sold_price');
            $table->string('source');
            $table->integer('checked')->default(1); /*numer of checked when compare data before update*/
            $table->integer('passed')->default(1); /*numer of passed tries when compare data before update*/
            $table->tinyInteger('matching_rate')->default(100); /*% value of match where add - default 100% when add new record*/
            $table->string('matched_by')->nullable(); /*fields used to match the record when add */
            $table->unsignedBigInteger('source_row_id'); /* source record id */
        });
    }
}
