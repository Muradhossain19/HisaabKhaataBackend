<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('party_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('party_id');
            $table->enum('direction', ['you_get', 'you_give']); // receivable / payable
            $table->decimal('amount', 14, 2)->default(0);
            $table->timestamp('date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('party_id')->references('id')->on('parties')->onDelete('cascade');
            $table->index(['user_id', 'party_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('party_ledger_entries');
    }
};

