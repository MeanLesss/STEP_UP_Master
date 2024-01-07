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
        Schema::create('top_up_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->double('balance')->nullable()->default(0.00);
            $table->string('card_number')->nullable()->comment('Hashed and when pass back to user show less');
            $table->string('card_name')->nullable()->comment('Hashed');
            $table->string('card_cvv')->nullable()->comment('Hashed');
            $table->string('card_date')->nullable()->comment('Hashed');
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
        Schema::dropIfExists('top_up_log');
    }
};
