<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tanks', function (Blueprint $table) {
            $table->id();
            $table->string('tank_code')->unique();
            $table->string('name');
            $table->string('tank_type')->default('GLASS');
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('volume_liters', 8, 2)->default(0);
            $table->string('location')->nullable();
            $table->string('purpose')->default('GROWOUT');
            $table->string('status')->default('ACTIVE');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanks');
    }
};
