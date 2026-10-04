<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('farm_name')->nullable()->after('password');
            $table->string('farm_location')->nullable()->after('farm_name');
            $table->string('gcash_name')->nullable()->after('farm_location');
            $table->string('gcash_number')->nullable()->after('gcash_name');
            $table->string('gcash_qr_path')->nullable()->after('gcash_number');
            $table->string('maya_name')->nullable()->after('gcash_qr_path');
            $table->string('maya_number')->nullable()->after('maya_name');
            $table->text('bank_details')->nullable()->after('maya_number');
            $table->text('shipping_notes')->nullable()->after('bank_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'farm_name',
                'farm_location',
                'gcash_name',
                'gcash_number',
                'gcash_qr_path',
                'maya_name',
                'maya_number',
                'bank_details',
                'shipping_notes',
            ]);
        });
    }
};
