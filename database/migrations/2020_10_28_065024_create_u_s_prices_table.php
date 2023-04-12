<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUSPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('u_s_prices', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->unique();

            $table->jsonb('EUR')->nullable();
            $table->jsonb('USD')->nullable();
            $table->jsonb('RUB')->nullable();
            $table->jsonb('TRY')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('u_s_prices');
    }
}
