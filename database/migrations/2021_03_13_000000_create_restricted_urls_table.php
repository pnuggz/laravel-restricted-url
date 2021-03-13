<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestrictedUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restricted_urls', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('route_name');
            $table->string('key');
            $table->dateTime('expires_at')->nullable();
            $table->integer('access_limit')->nullable()->default(2);
            $table->integer('access_count')->default(0);
            $table->ipAddress('first_accessed_by_ip')->nullable();
            $table->dateTime('first_accessed_at')->nullable();
            $table->ipAddress('last_reaccessed_by_ip')->nullable();
            $table->dateTime('last_reaccessed_at')->nullable();
            $table->bigInteger('created_by_user_id')->unsigned();
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
        Schema::dropIfExists('restricted_urls');
    }
}