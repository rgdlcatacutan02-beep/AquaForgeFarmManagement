<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offspring_batch_id')->constrained('offspring_batches')->cascadeOnDelete();
            $table->enum('type', ['MORTALITY', 'CULLING', 'ADJUSTMENT']);
            $table->unsignedInteger('quantity');
            $table->date('log_date');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_logs');
    }
};
