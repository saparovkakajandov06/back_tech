<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUzsToPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('u_s_prices', function (Blueprint $table) {
            $table->jsonb('UZS')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('u_s_prices', ['UZS'])) {
            Schema::table('u_s_prices', function (Blueprint $table) {
                $table->dropColumn('UZS');
            });
        }
    }
}
