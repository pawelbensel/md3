<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('commission');
            $table->string('participation');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('commissioner_id')->nullable();
            $table->string('commissioner_type')->nullable();
            $table->string('source');
            $table->integer('checked')->default(1); /*numer of checked when compare data before update*/
            $table->integer('passed')->default(1); /*numer of passed tries when compare data before update*/
            $table->tinyInteger('matching_rate')->default(100); /*% value of match where add - default 100% when add new record*/
            $table->string('matched_by')->nullable(); /*fields used to match the record when add */
            $table->unsignedBigInteger('source_row_id'); /* source record id */
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
        Schema::dropIfExists('commissions');
    }
}
