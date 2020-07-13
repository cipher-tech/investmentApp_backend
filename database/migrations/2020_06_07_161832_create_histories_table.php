<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->string("user_id");
            $table->string("plan");
            $table->integer("amount");
            $table->integer("earnings");
            $table->integer("duration");
            $table->integer("rate");
            $table->string("type");
            $table->string("status");
            $table->string("image");
            $table->integer("withdrawl");
            $table->integer("deposit");
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
        Schema::dropIfExists('histories');
    }
}
