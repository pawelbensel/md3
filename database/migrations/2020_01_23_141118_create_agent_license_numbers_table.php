<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentLicenseNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_license_numbers', function (Blueprint $table) {        
            $table->bigIncrements('id');
            $table->unsignedBigInteger('agent_id');
            $table->string('license_number');            
            $table->string('slug');            
            $table->timestamps();
            $table->foreign('agent_id')->references('id')->on('agents');
            $table->string('source');
            $table->integer('checked')->default(1); /*numer of checked when compare data before update*/
            $table->integer('passed')->default(1); /*numer of passed tries when compare data before update*/
            $table->tinyInteger('matching_rate')->default(100); /*% value of match where add - default 100% when add new record*/
            $table->string('matched_by')->nullable(); /*fields used to match the record when add */
            $table->unsignedBigInteger('source_row_id'); /* source record id */
            $table->index('type');
            $table->index('slug');
            $table->index('license_number');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_license_numbers');
    }
}
