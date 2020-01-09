<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourceObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('source_objects', function (Blueprint $table) {
            $table->bigIncrements('id');            
            $table->string('hash');
            $table->json('object');            
            $table->string('object_type');
            $table->string('source');
            $table->boolean('parsed')->default(false);
            $table->timestamps();
            $table->index('hash');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('source_objects');
    }
}
