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
        Schema::create('delay_notifications', function (Blueprint $table) {
    $table->id();
    $table->string('trip_id');
    $table->string('route_name');
    $table->dateTime('departure_time')->nullable();
    $table->integer('delay_minutes');
    $table->text('delay_reason');
    $table->enum('notification_status', ['pending', 'sent'])->default('pending');
    $table->string('passenger_name');
    $table->string('passenger_email');
    $table->timestamp('sent_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delay_notifications');
    }
};
