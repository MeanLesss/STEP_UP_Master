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
        Schema::create('service_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Service_id');
            $table->unsignedBigInteger('order_by');
            $table->unsignedBigInteger('cancel_by');
            $table->boolean('isCancel')->default(0)->comment('0:false,1:true');
            $table->date('cancel_at')->nullable()->comment('use required if is cancel');
            $table->text('cancel_desc')->nullable()->comment('use required if is cancel');
            $table->string('order_title');
            $table->text('order_description');
            $table->integer('order_status')->default(0)->comment('-1 :Declined,0 :pending, 1 :in progress ,2 :In Review,3 :Success, 4 :Fail');
            $table->json('order_attachments')->comment('[{0 :"Path1"},{ 1 :"path2"}]');
            $table->date('expected_expand_date')->nullable()->comment('this will send the notification to the client for the date expand than will update expand_due_date');
            $table->date('expand_end_date')->nullable()->comment('Only when the freelancer requested and accepted by user expand time on project');
            $table->date('expected_start_date')->comment('Expectation from client');
            $table->date('expected_end_date')->comment('to automatically take down when it reach the date use trigger');
            $table->timestamp('Accepted_At')->nullable();
            $table->unsignedBigInteger('Created_By');
            $table->unsignedBigInteger('Updated_By')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order');
    }
};
