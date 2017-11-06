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
            $table->string('fullName');
            $table->string('phoneNumber');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('address');
            $table->string('certification');
            $table->string('certificationId');
            $table->integer('role_id');
            $table->string('pickUpDate')->nullable();
            $table->string('testingFrequency')->nullable();
            $table->string('testingLocation')->nullable();
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
