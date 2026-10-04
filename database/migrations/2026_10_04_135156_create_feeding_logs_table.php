<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feeding_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tank_id')->constrained('tanks')->cascadeOnDelete();
            $table->foreignId('livestock_id')->nullable()->constrained('livestock')->nullOnDelete();
            $table->foreignId('offspring_batch_id')->nullable()->constrained('offspring_batches')->nullOnDelete();
            $table->string('food');
            $table->string('quantity')->nullable();
            $table->dateTime('fed_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tank_id', 'fed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feeding_logs');
    }
};
