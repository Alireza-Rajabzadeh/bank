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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('status_id')->references('id')->on('transaction_statuses')->default(1);
            $table->foreignId('type_id')->references('id')->on('transaction_types')->default(1);
            $table->foreignId('parrent_transaction_id')->references('id')->on('transactions')->nullable();

            $table->foreignId('origin_card_id')->references('id')->on('cards');

            $table->foreignId('destination_card_id')->references('id')->on('cards');

            $table->decimal('ammount', 15, 2);
            $table->longText('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
