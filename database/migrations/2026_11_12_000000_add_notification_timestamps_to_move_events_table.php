<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('move_events', function (Blueprint $table) {
            $table->timestamp('confirmation_sent_at')->nullable()->after('notes');
            $table->timestamp('reminder_sent_at')->nullable()->after('confirmation_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('move_events', function (Blueprint $table) {
            $table->dropColumn(['confirmation_sent_at', 'reminder_sent_at']);
        });
    }
};
