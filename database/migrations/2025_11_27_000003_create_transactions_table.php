<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable(); // optional client-side temp id
            $table->enum('type', ['income', 'expense'])->default('expense');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('currency')->default('BDT');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->timestamp('date')->nullable();
            $table->text('note')->nullable();
            $table->text('attachments')->nullable(); // JSON array of urls
            $table->boolean('is_synced')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
