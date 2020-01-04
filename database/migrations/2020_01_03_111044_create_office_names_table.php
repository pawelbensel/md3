<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('office_names', function (Blueprint $table) {            
            $table->bigIncrements('id');
            $table->unsignedBigInteger('office_id');
            $table->string('name');
            $table->string('slug');
            $table->string('source');
            $table->timestamps();
            $table->foreign('office_id')->references('id')->on('offices');
            $table->index('name');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_names');
    }
}
