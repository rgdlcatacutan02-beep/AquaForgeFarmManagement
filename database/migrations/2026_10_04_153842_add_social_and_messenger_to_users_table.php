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
            $table->string('messenger_username')->nullable()->after('shipping_notes');
            $table->string('facebook_page')->nullable()->after('messenger_username');
            $table->string('contact_number')->nullable()->after('facebook_page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'messenger_username',
                'facebook_page',
                'contact_number',
            ]);
        });
    }
};
