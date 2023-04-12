<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proxies', function (Blueprint $table) {
            $table->increments('id');

//    proxy: '45.81.76.81:8000',
//    inst: 'shalon_reaume_238:qcasxDNRjdQK',
//    cookie: 'ig_did=DDAA52C2-38B1-4173-9525-28B141B921A0; mid=X-Q8UwALAAFY67qN5mo0D7w-6yiK; ig_nrcb=1; shbid=6630; shbts=1608816467.6336243; rur=FTW; urlgen="{\\"45.10.80.67\\": 49505\\054 \\"45.81.76.81\\": 35751}:1ksprs:UFQ5lrsyoC2rTskMuMKkLripqBI"; csrftoken=uYzj7Ygt7mQcEDd7FfuaOOsM1BGsPcQv; ds_user_id=457532788; sessionid=457532788%3A6XMYvXV7SOMKHo%3A25',
//    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0'

            $table->string('comment')->nullable();
            $table->string('url');
            $table->string('instagram')->nullable();
            $table->text('cookie')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('enabled')->default(false);

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
        Schema::dropIfExists('proxies');
    }
}
