<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('moving_quotes', function (Blueprint $table) {
            // Step 1: contact & date (new redesigned flow)
            $table->boolean('sms_consent')->default(false)->after('phone');
            $table->boolean('date_flexible')->default(false)->after('preferred_date');
            $table->string('origin_zip', 10)->nullable()->after('origin_address');
            $table->string('destination_zip', 10)->nullable()->after('destination_address');

            // Step 2: size of the move
            $table->string('bedrooms')->nullable()->after('move_type');
            $table->json('special_items')->nullable()->after('packing_service');
            $table->json('photos')->nullable()->after('special_items');
        });

        // The old flow required a full address, a full email and a fixed set of
        // move_type values. The new flow only asks for a ZIP code at step 1 and
        // widens move_type, so relax those constraints without touching data
        // already stored for existing quotes. Raw statements are used because
        // this project does not have doctrine/dbal, which Laravel's
        // Blueprint::change() needs on Laravel 10.
        DB::statement('ALTER TABLE moving_quotes MODIFY name VARCHAR(255) NULL');
        DB::statement('ALTER TABLE moving_quotes MODIFY email VARCHAR(255) NULL');
        DB::statement('ALTER TABLE moving_quotes MODIFY origin_address VARCHAR(255) NULL');
        DB::statement('ALTER TABLE moving_quotes MODIFY destination_address VARCHAR(255) NULL');
        DB::statement("ALTER TABLE moving_quotes MODIFY move_type VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moving_quotes', function (Blueprint $table) {
            $table->dropColumn([
                'sms_consent',
                'date_flexible',
                'origin_zip',
                'destination_zip',
                'bedrooms',
                'special_items',
                'photos',
            ]);
        });

        DB::statement("ALTER TABLE moving_quotes MODIFY move_type ENUM('residential', 'office', 'storage') NULL");
        DB::statement('ALTER TABLE moving_quotes MODIFY destination_address VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE moving_quotes MODIFY origin_address VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE moving_quotes MODIFY email VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE moving_quotes MODIFY name VARCHAR(255) NOT NULL');
    }
};
