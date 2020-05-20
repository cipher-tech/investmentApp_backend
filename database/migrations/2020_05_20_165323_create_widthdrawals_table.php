<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWidthdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widthdrawals', function (Blueprint $table) {
            $table->id();
            $table->string("user_id")->unique();
            $table->string("coin_address")->nullable();
            $table->string("plan")->nullable();
            $table->integer("amount")->nullable();
            $table->string("status");
            $table->string("slug");
            $table->string("wallet_balc")->nullable();
            $table->string("trans_type")->nullable();
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
        Schema::dropIfExists('widthdrawals');
    }
}
