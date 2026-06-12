<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delay_notifications', function (Blueprint $table) {
            $table->string('receipt_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('delay_notifications', function (Blueprint $table) {
            $table->dropColumn('receipt_number');
        });
    }
};