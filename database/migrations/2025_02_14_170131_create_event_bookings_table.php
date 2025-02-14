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
        Schema::create('event_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->json('tickets'); // Stores ticket type and quantity in JSON format
            $table->integer('total_price'); // Total amount paid
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            
            // Payment Details
            $table->string('card_holder_name'); // Name on the card
            $table->string('card_last_four', 4); // Last 4 digits of the card
            $table->string('card_type'); // Visa, MasterCard, etc.
            $table->string('transaction_id')->nullable(); // Unique transaction ID for tracking
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_bookings');
    }
};
