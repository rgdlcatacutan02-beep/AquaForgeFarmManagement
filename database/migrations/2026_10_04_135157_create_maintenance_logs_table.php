<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tank_id')->constrained('tanks')->cascadeOnDelete();
            $table->string('maintenance_type');
            $table->dateTime('performed_at');
            $table->unsignedTinyInteger('water_change_percentage')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tank_id', 'performed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
