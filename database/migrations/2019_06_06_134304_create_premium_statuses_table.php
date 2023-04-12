<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePremiumStatusesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('premium_statuses', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');

            $table->boolean('online_support')->default(0);
            $table->boolean('personal_manager')->default(0);

            // array
            $table->text('discount');

            $table->integer('cash')->default('0');

            $table->timestamps();

            $table->string('cur')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('premium_statuses');
    }
}
