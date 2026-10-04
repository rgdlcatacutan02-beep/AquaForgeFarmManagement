<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tank_id')->constrained('tanks')->cascadeOnDelete();
            $table->dateTime('recorded_at');
            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('ph', 4, 2)->nullable();
            $table->decimal('ammonia', 5, 2)->nullable();
            $table->decimal('nitrite', 5, 2)->nullable();
            $table->decimal('nitrate', 5, 2)->nullable();
            $table->decimal('tds', 6, 2)->nullable();
            $table->enum('status', ['GOOD', 'WARNING', 'CHECK'])->default('GOOD');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tank_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_logs');
    }
};
