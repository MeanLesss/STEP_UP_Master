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
        Schema::create('service', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->integer('status')->default(0);
            $table->integer('view')->default(0);
            $table->integer('service_rate')->default(0);
            $table->integer('service_ordered_count')->default(0);
            $table->json('attachments')->default('{}')->nullable();
            $table->string('requirement')->nullable();
            $table->double('price')->default(5.00)->comment('Greater than $5');
            $table->double('discount')->default(0.00)->nullable();
            $table->string('service_type')->nullable()->comment('Software Developement, Graphic Design : as dropdown');
            $table->date('start_date');
            $table->date('end_date');
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
        Schema::dropIfExists('service');
    }
};
