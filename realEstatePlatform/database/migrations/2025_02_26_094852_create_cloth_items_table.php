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
        Schema::create('cloth_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');//->constrained()->onDelete('cascade');
            $table->string('description')->nullable();
            $table->string('category')->nullable(); 
            $table->string('brand')->nullable();
            $table->string('image_link')->nullable();
            $table->boolean('favorite')->default(false);
            $table->decimal('price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
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
        Schema::dropIfExists('cloth_items');
    }
};
