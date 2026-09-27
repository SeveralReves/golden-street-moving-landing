<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moving_quotes', function (Blueprint $table) {
            $table->decimal('estimate_total', 10, 2)->nullable()->after('photos');
            $table->decimal('estimate_range_low', 10, 2)->nullable()->after('estimate_total');
            $table->decimal('estimate_range_high', 10, 2)->nullable()->after('estimate_range_low');
            $table->decimal('estimate_hours', 6, 2)->nullable()->after('estimate_range_high');
            $table->json('estimate_breakdown')->nullable()->after('estimate_hours');
        });
    }

    public function down(): void
    {
        Schema::table('moving_quotes', function (Blueprint $table) {
            $table->dropColumn([
                'estimate_total',
                'estimate_range_low',
                'estimate_range_high',
                'estimate_hours',
                'estimate_breakdown',
            ]);
        });
    }
};
