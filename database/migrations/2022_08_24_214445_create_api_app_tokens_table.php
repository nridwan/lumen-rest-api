<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiAppTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_app_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("app_id")->nullable();
            $table->string('hash');
            $table->timestamp('expired_at');
            $table->foreign("app_id")->references("id")->on("api_apps")->onDelete("cascade")->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_tokens');
    }
}
