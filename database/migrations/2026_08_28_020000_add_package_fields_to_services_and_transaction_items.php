<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_package')->default(false)->after('price');
            $table->unsignedInteger('session_count')->nullable()->after('is_package');
        });

        Schema::table('loyalty_transaction_items', function (Blueprint $table) {
            $table->unsignedInteger('session_count')->nullable()->after('original_price');
            $table->unsignedInteger('sessions_redeemed')->nullable()->after('session_count');
            $table->boolean('is_package_redemption')->default(false)->after('sessions_redeemed');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_transaction_items', function (Blueprint $table) {
            $table->dropColumn(['session_count', 'sessions_redeemed', 'is_package_redemption']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['is_package', 'session_count']);
        });
    }
};
