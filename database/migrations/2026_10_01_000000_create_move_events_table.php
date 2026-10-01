<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('move_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moving_quote_id')->nullable()->constrained('moving_quotes')->nullOnDelete();
            $table->string('title');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('origin_address')->nullable();
            $table->string('destination_address')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->unsignedTinyInteger('crew_size')->default(2);
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['start_at', 'end_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('move_events');
    }
};
