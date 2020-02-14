<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMergeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merge_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('similar_id');
            $table->unsignedBigInteger('target_id');
            $table->unsignedBigInteger('previous_id');
            $table->string('target_type');
            $table->string('previous_type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merge_histories');
    }
}
