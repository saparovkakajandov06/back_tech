<?php

use App\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompositeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('composite_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('user_service_id');

            $table->boolean('paid')->default('0');

            // array
//            $table->text('params')->nullable();
            $table->jsonb('params')->nullable();

            $table->string('status')->default(Order::STATUS_CREATED);
            $table->boolean('done')->default('0');

            // google analytics
            $table->string('session_id')->nullable();

            $table->timestamps();

            $table->string('uuid')->unique()->nullable();

            // indexes
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('composite_orders');
    }
}
