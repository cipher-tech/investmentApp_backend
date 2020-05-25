<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('slug');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone_no')->unique();
            $table->string('password');
            $table->string('Dob')->nullable();
            $table->string('coutry')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('status')->default("unverified");
            $table->integer('wallet_balc')->default(0);
            $table->string("current_plan")->nullable();
            $table->string('coin_address')->nullable();
            $table->string('widthdrawals_id')->nullable();
            $table->string('role')->nullable();
            $table->string('zip_code')->nullable();
            $table->integer('earnings')->default(0);
            $table->string('ref_code')->nullable();
            $table->string('auth_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
