<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaleToPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('u_s_prices', function (Blueprint $table) {
            $table->jsonb('sale')->nullable();
            $table->jsonb('hot')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('u_s_prices', ['sale', 'hot'])) {
            Schema::table('u_s_prices', function (Blueprint $table)
            {
                $table->dropColumn('sale');
                $table->dropColumn('hot');
            });
        }
    }
}
