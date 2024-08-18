<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->bigInteger('address_id')->nullable();
            $table->foreign('address_id')->references('id')->on('user_addresses')->nullOnDelete();
            $table->string('phone');
            $table->string('email');
            $table->dateTime('delivery_time')->nullable();
            $table->unsignedBigInteger('cost');
            $table->enum('status', ['created', 'in_process', 'done', 'canceled'])->default('created');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
