<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB as DB;

class UpdateUserServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('user_services', 'config'))
        {
            DB::statement("ALTER TABLE user_services ALTER config TYPE JSONB USING (config)::jsonb");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_services', 'config'))
        {
            DB::statement('ALTER TABLE user_services ALTER config TYPE TEXT;');
        }
    }
}