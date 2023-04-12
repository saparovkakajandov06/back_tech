<?php

use App\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChunksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chunks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('composite_order_id')->nullable();

            $table->string('service_class');

            $table->jsonb('details'); // count, link etc.
            $table->integer('completed')->default('0');

            $table->string('extern_id')->nullable();
            $table->string('status')->default(Order::STATUS_CREATED);
            $table->text('add_request')->nullable();
            $table->text('remote_response')->nullable();
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
        Schema::dropIfExists('chunks');
    }
}
