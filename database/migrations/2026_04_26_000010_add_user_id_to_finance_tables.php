<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->index(['user_id', 'date']);
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->index(['user_id', 'name']);
            }
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_methods', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                $table->index(['user_id', 'name']);
            }
        });
    }

    public function down()
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (Schema::hasColumn('payment_methods', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};

