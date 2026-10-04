<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('unit')->default('pcs');
            $table->decimal('minimum_quantity', 10, 2)->default(0);
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
