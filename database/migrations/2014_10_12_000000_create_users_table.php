<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');

            $table->string('api_token', 60)->unique()->nullable();
            $table->timestamp('token_updated_at')->nullable();

            $table->string('name')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->string('reset_code')->nullable();
            $table->rememberToken();
            $table->timestamps();

//            $table->string('vk_id')->nullable();
//            $table->string('vk_token')->nullable();

//            $table->string('fb_id')->nullable();
//            $table->string('fb_token')->nullable();

            $table->text('roles')->nullable();

//            $table->string('in_id')->nullable();
            $table->string('instagram_login')->nullable();

            $table->string('social_id')->nullable();
            $table->string('network')->nullable();
            $table->text('avatar')->nullable();

            $table->string('ref_code')->unique()->nullable();
            $table->integer('parent_id')->nullable();

            $table->integer('premium_status_id')->default('1');

            $table->string('telegram_id')->unique()->nullable();

            $table->string('lang')->default('ru');
            $table->string('cur')->default('RUB');

            $table->jsonb('params')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
