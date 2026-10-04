<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offspring_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code')->unique();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->foreignId('breeding_event_id')->nullable()->constrained('breeding_events')->nullOnDelete();
            $table->string('variety')->nullable();
            $table->date('birth_or_hatch_date')->nullable();
            $table->unsignedInteger('initial_count')->default(0);
            $table->unsignedInteger('current_count')->default(0);
            $table->unsignedInteger('death_count')->default(0);
            $table->unsignedInteger('cull_count')->default(0);
            $table->unsignedInteger('available_count')->default(0);
            $table->string('grade')->nullable();
            $table->foreignId('tank_id')->nullable()->constrained('tanks')->nullOnDelete();
            $table->string('status')->default('GROWING');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['species_id', 'status']);
            $table->index(['tank_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offspring_batches');
    }
};
