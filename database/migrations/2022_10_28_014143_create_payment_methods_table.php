<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order');
            $table->string('icon')->nullable();
            $table->jsonb('titles');
            $table->jsonb('currencies');
            $table->jsonb('limits');
            $table->jsonb('countries');
            $table->string('payment_system');
            $table->string('gate_method_id')->nullable();
            $table->string('country_filter');
            $table->boolean('show_agreement_flag');

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
        Schema::dropIfExists('payment_methods');
    }
}
