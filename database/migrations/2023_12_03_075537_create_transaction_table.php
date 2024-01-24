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
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('free_id');
            $table->unsignedBigInteger('order_id');
            $table->integer('client_status')->default(0)->comment('0:pending,1:failed,2:accepted');;
            $table->integer('freelancer_status')->default(0)->comment('0:pending,1:failed,2:accepted');
            $table->boolean('isComplain')->default(0)->comment('use required if is cancel');
            $table->integer('rate')->nullable()->comment('1 -> 5');
            $table->json('tranc_attachments')->default(DB::raw('(JSON_OBJECT())'))->nullable();
            $table->integer('tranc_status')->default(0)->comment('0:pending,1:failed,2:accepted');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
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
