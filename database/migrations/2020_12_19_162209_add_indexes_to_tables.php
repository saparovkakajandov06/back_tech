<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (App::environment('testing')) {
                return;
            }
            try {
                DB::statement("create extension pg_trgm");
                DB::statement("create index co_login_idx on composite_orders using gin ((params->>'login') gin_trgm_ops)");
                DB::statement("create index co_link_idx on composite_orders using gin ((params->>'link') gin_trgm_ops)");

                DB::statement('create index transactions_user_id_idx on transactions(user_id)');

                DB::statement('create index co_user_id_idx on composite_orders(user_id)');

                DB::statement('create index users_index_name on users using gin (name gin_trgm_ops)');
                DB::statement('create index users_index_email on users using gin (email gin_trgm_ops)');
            } catch (\Throwable $e) {
                echo __METHOD__ . ' ' . $e->getMessage() . "\n";
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
        });
    }
}
