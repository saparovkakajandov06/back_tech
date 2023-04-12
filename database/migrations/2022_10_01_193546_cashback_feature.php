<?php

use App\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CashbackFeature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashback', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('type');
            $table->decimal('amount', 19, 4);
            $table->string('cur')->default(Transaction::CUR_RUB);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('user_id', 'payments_user_id_foreign')->references('id')->on('users');

            $table->renameColumn('type', 'payment_system');
            $table->jsonb('actions')->default('[]')->after('currency');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cashback');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_user_id_foreign');

            $table->renameColumn('payment_system', 'type');
            $table->dropColumn('actions');
        });
    }
}
 