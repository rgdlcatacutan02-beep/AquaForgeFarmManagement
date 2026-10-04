<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock', function (Blueprint $table) {
            $table->id();
            $table->string('livestock_code')->unique();
            $table->foreignId('species_id')->constrained('species')->cascadeOnDelete();
            $table->string('variety')->nullable();
            $table->enum('sex', ['MALE', 'FEMALE', 'UNKNOWN'])->default('UNKNOWN');
            $table->date('date_of_birth')->nullable();
            $table->date('date_acquired')->nullable();
            $table->string('source')->nullable();
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->string('status')->default('GROWOUT');
            $table->foreignId('tank_id')->nullable()->constrained('tanks')->nullOnDelete();
            $table->string('grade')->nullable();
            $table->json('grading_scores')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['species_id', 'status']);
            $table->index(['tank_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock');
    }
};
