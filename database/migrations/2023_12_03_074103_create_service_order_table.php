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
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('freelancer_id')->nullable();
            $table->unsignedBigInteger('order_by');
            $table->unsignedBigInteger('cancel_by')->nullable();
            $table->boolean('isCancel')->nullable()->default(0)->comment('0:false,1:true');
            $table->boolean('isAgreementAgreed')->nullable()->default(0)->comment('0:false,1:true');
            $table->date('accepted_at')->nullable()->comment(' ');
            $table->date('start_at')->nullable()->comment(' ');
            $table->date('cancel_at')->nullable()->comment('use required if is cancel');
            $table->text('cancel_desc')->nullable()->comment('use required if is cancel');
            $table->string('order_title');
            $table->text('order_description');
            $table->integer('order_status')->default(0)->comment('-1 :Declined,0 :pending, 1 :in progress ,2 :In Review,3 :Success, 4 :Fail');
            $table->json('completed_attachments')->default(DB::raw('(JSON_ARRAY())'))->nullable()->comment('[{0 :"Path1"},{ 1 :"path2"}]');
            $table->json('order_attachments')->default(DB::raw('(JSON_ARRAY())'))->nullable()->comment('[{0 :"Path1"},{ 1 :"path2"}]');
            $table->date('expected_expand_date')->nullable()->comment('this will send the notification to the client for the date expand than will update expand_due_date');
            $table->date('expand_end_date')->nullable()->comment('Only when the freelancer requested and accepted by user expand time on project');
            $table->date('expected_start_date')->comment('Expectation from client');
            $table->date('expected_end_date')->comment('to automatically take down when it reach the date use trigger');
            $table->timestamp('accepted_at')->nullable();
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
        Schema::dropIfExists('service_order');
    }
};
