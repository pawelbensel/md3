<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficeMlsIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('office_mls_ids');
        //Schema::defaultStringLength(191);
        Schema::create('office_mls_ids', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('office_id');
            $table->string('mls_id');
            $table->string('mls_name');
            $table->timestamps();
            $table->foreign('office_id')->references('id')->on('offices');
            $table->index('mls_name');
            $table->index('mls_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('office_mls_ids');
    }
}
