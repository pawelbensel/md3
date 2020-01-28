<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfficeNameIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE office_names ADD FULLTEXT office_names_name_fulltext_index (`name`)');
        DB::statement('ALTER TABLE office_names ADD FULLTEXT office_names_slug_fulltext_index (`slug`)');
        DB::statement('ALTER TABLE office_addresses ADD FULLTEXT office_addresses_city_fulltext_index (`city`)');
        DB::statement('ALTER TABLE office_addresses ADD FULLTEXT office_addresses_address1_fulltext_index (`address1`)');
        DB::statement('ALTER TABLE office_addresses ADD FULLTEXT office_addresses_address2_fulltext_index (`address2`)');
        DB::statement('ALTER TABLE office_phones ADD FULLTEXT office_phones_phone_fulltext_index (`phone`)');
        DB::statement('ALTER TABLE office_phones ADD FULLTEXT office_phones_slug_fulltext_index (`slug`)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP INDEX office_names_name_fulltext_index on office_names ');
        DB::statement('DROP INDEX office_names_slug_fulltext_index on office_names ');
        DB::statement('DROP INDEX office_addresses_city_fulltext_index on office_addresses');
        DB::statement('DROP INDEX office_addresses_address1_fulltext_index on office_addresses');
        DB::statement('DROP INDEX office_addresses_address2_fulltext_index on office_addresses');
        DB::statement('DROP INDEX office_phones_phone_fulltext_index on office_phones');
        DB::statement('DROP INDEX office_phones_slug_fulltext_index on office_phones');
    }
    
}
