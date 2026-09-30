<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Widen the enum first so existing rows can be renamed without data loss.
        DB::statement("ALTER TABLE moving_quotes MODIFY status ENUM('pending','in_review','quoted','schedule','closed','cancelled') NOT NULL DEFAULT 'pending'");
        DB::table('moving_quotes')->where('status', 'quoted')->update(['status' => 'schedule']);
        DB::statement("ALTER TABLE moving_quotes MODIFY status ENUM('pending','in_review','schedule','closed','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE moving_quotes MODIFY status ENUM('pending','in_review','quoted','schedule','closed','cancelled') NOT NULL DEFAULT 'pending'");
        DB::table('moving_quotes')->where('status', 'schedule')->update(['status' => 'quoted']);
        DB::statement("ALTER TABLE moving_quotes MODIFY status ENUM('pending','in_review','quoted','closed','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
