<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMd3ColumnsToTables extends Migration
{


    private $tables = [
         'agent_emails',     
         'agent_first_names',
         'agent_last_names',
         'agent_mls_ids',
         'agent_phones',
         'agent_titles',
         'agent_types',
         'office_addresses',
         'office_company_names',
         'office_emails',
         'office_mls_ids',       
         'office_msa_ids',       
         'office_names',         
         'office_phones',        
         'office_states',        
         'office_zips',
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $tableName){
            Schema::table($tableName, function (Blueprint $table) {                
                $table->string('source');
                $table->integer('checked')->default(1); /*numer of checked when compare data before update*/
                $table->integer('passed')->default(1); /*numer of passed tries when compare data before update*/
                $table->tinyInteger('matching_rate')->default(100); /*% value of match where add - default 100% when add new record*/
                $table->string('matched_by')->nullable(); /*fields used to match the record when add */
                $table->unsignedBigInteger('source_row_id'); /* source record id */
            });    
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $tableName){
            Schema::table($tableName, function (Blueprint $table) {                                
                $table->dropColumn('source');
                $table->dropColumn('checked'); /*numer of checked when compare data before update*/
                $table->dropColumn('passed'); /*numer of passed tries when compare data before update*/
                $table->dropColumn('matching_rate'); /*% value of match where add - default 100% when add new record*/
                $table->dropColumn('matched_by'); /*fields used to match the record when add */
                $table->dropColumn('source_row_id'); /* source record id */
            });
        }
    }
}
