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
        Schema::create('complain_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tranc_id');
            $table->unsignedBigInteger('tranc_user_id');
            $table->unsignedBigInteger('tranc_service_id');
            $table->string('title')->comment('use required if is cancel is complain ');;
            $table->text('description')->comment('use required if is cancel when is complain ');;
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
        Schema::dropIfExists('complain_detail');
    }
};
