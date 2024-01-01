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
        Schema::create('user_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('phone')->nullable();
            $table->string('id_card_no')->nullable();
            $table->string('job_type',45)->nullable();
            $table->string('id_attachment')->nullable()->comment('image path');
            $table->string('profile_image')->nullable()->comment('image path');
            $table->string('card_number')->nullable()->comment('Hashed and when pass back to user show less');
            $table->string('card_name')->nullable()->comment('Hashed');
            $table->string('card_cvv')->nullable()->comment('Hashed');
            $table->string('card_date')->nullable()->comment('Hashed');
            $table->integer('credit_score')->default(100)->comment('max : 100,80 :warning, 50 : 3 days ban,30: 7days ,25:permanent');
            $table->double('balance')->default(0.00)->comment('$0.01');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_detail');
    }
};
