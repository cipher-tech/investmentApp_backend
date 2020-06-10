<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans_users', function (Blueprint $table) {
            $table->id();
            $table->string("plan_id");
            $table->string("user_id");
            $table->integer("amount");
            $table->integer("count");
            $table->integer("duration");
            $table->integer("rate");
            $table->integer("earnings");
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
        Schema::dropIfExists('plans_users');
    }
}
