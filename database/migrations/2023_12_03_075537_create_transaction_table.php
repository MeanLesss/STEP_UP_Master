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
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id');
            $table->integer('client_status')->default(0)->comment('0:pending,1:failed,2:accepted');;
            $table->integer('freelancer_status')->default(0)->comment('0:pending,1:failed,2:accepted');
            $table->boolean('isComplain')->default(0)->comment('use required if is cancel');
            $table->integer('rate')->nullable()->comment('1 -> 5');
            $table->integer('tranc_status')->default(0)->comment('0:pending,1:failed,2:accepted');
            $table->unsignedBigInteger('created_By');
            $table->unsignedBigInteger('updated_By');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
