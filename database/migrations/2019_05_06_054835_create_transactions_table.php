<?php

use App\Transaction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('event_id')->nullable();
            $table->string('type');
            $table->decimal('amount', 19, 4);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->integer('related_user_id')->nullable();

            $table->decimal('commission', 19, 4)->default(0);

            $table->string('cur')->default(Transaction::CUR_RUB);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('transactions');
    }
}
