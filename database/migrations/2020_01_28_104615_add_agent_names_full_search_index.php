<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgentNamesFullSearchIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE agent_first_names ADD FULLTEXT agent_first_names_first_name_fulltext_index (`first_name`)');
        DB::statement('ALTER TABLE agent_first_names ADD FULLTEXT agent_first_names_slug_fulltext_index (`slug`)');
        DB::statement('ALTER TABLE agent_last_names ADD FULLTEXT agent_last_names_last_name_fulltext_index (`last_name`)');
        DB::statement('ALTER TABLE agent_last_names ADD  FULLTEXT agent_last_names_slug_fulltext_index (`slug`)');        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX agent_first_names_first_name_fulltext_index on agent_first_names ');
        DB::statement('DROP INDEX agent_first_names_slug_fulltext_index on agent_first_names ');
        DB::statement('DROP INDEX agent_last_names_last_name_fulltext_index on agent_last_names');
        DB::statement('DROP INDEX agent_last_names_slug_fulltext_index on agent_last_names');        
    }
}
