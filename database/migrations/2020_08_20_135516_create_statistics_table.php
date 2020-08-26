<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->integer("no_visits");
            $table->integer("home_page");
            $table->integer("about_page");
            $table->integer("faq_page");
            $table->integer("buy_sell_page");
            $table->integer("rate_page");
            $table->integer("contact_page");
            $table->integer("policies_page");
            $table->integer("login_page");
            $table->integer("signUp_page");
            $table->integer("user_admin_page");
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
        Schema::dropIfExists('statistics');
    }
}
