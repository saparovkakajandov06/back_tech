<?php

use App\UserService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_services', function (Blueprint $table) {
            // ??? unused fields

            $table->increments('id');
            $table->string('title');
            $table->string('tag')->unique();

            $table->jsonb('pipeline')->nullable();
            $table->string('splitter');
            $table->text('config')->nullable();

            $table->string('img')->nullable();

            $table->timestamps();

            // информация
            $table->text('description')->nullable();
            $table->text('card')->nullable();

            // Заработок пользователей
            $table->text('local_validation')->nullable();
            $table->string('local_checker')->nullable();

            $table->string('tracker')->nullable();

            $table->string('platform')->nullable();
            $table->string('name')->nullable();

            $table->jsonb('labels')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_services');
    }
}
