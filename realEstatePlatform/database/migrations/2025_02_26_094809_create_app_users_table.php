<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string("email")->unique();
            $table->string("password");
            $table->string('contact')->nullable();
            $table->enum("gender",["F","M"])->nullable();
            $table->string("profile_picture")->default("COcY0ydd9ZJx3Tfz5qyPB06SLFvnh7qWB8hMAZfI.jpg")->nullable();
            $table->timestamp('email_verified_at')->nullable();
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
        Schema::dropIfExists('app_users');
    }
};
