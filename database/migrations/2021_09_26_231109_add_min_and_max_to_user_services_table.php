<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinAndMaxToUserServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_services', function (Blueprint $table) {
            $table->integer('min_order')->nullable();
            $table->integer('max_order')->nullable();
            $table->string('order_speed')->nullable();
            $table->string('order_frequency')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumns('user_services', ['min_order', 'max_order', 'order_speed', 'order_frequency']))
        {
            Schema::table('user_services', function (Blueprint $table)
            {
                $table->dropColumn('min_order');
                $table->dropColumn('max_order');
                $table->dropColumn('order_speed');
                $table->dropColumn('order_frequency');
            });
        }
    }
}