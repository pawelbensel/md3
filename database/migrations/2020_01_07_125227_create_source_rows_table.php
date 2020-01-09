<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSourceRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('source_rows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash');
            $table->json('row');
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
        Schema::dropIfExists('source_rows');
    }
}
