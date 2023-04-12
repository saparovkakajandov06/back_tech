<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCashbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashback', function (Blueprint $table) {
            $table->integer('payment_id')->nullable();
            $table->jsonb('order_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('cashback', ['payment_id', 'order_ids'])) {
            Schema::table('cashback', function (Blueprint $table)
            {
                $table->dropColumn('payment_id');
                $table->dropColumn('order_ids');
            });
        }
    }
}
