<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breeding_events', function (Blueprint $table) {
            $table->id();
            $table->string('breeding_code')->unique();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->foreignId('tank_id')->nullable()->constrained('tanks')->nullOnDelete();
            $table->foreignId('male_livestock_id')->nullable()->constrained('livestock')->nullOnDelete();
            $table->foreignId('female_livestock_id')->nullable()->constrained('livestock')->nullOnDelete();
            $table->date('start_date');
            $table->date('expected_date')->nullable();
            $table->date('actual_birth_or_hatch_date')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['species_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breeding_events');
    }
};
