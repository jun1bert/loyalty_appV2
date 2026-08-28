<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->foreignId('promo_code_id')
                ->nullable()
                ->after('processed_by')
                ->constrained('promo_codes')
                ->nullOnDelete();
            $table->string('promo_code')->nullable()->after('promo_code_id');
            $table->decimal('promo_discount_amount', 10, 2)->default(0)->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('promo_code_id');
            $table->dropColumn(['promo_code', 'promo_discount_amount']);
        });
    }
};
