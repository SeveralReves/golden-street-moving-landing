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
        Schema::create('moving_quotes', function (Blueprint $table) {
            $table->id();
            
            // Step 1: Contact info & base details
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            // Origin and destination
            $table->string('origin_address');
            $table->decimal('origin_lat', 10, 7)->nullable();
            $table->decimal('origin_lng', 10, 7)->nullable();

            $table->string('destination_address');
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_lng', 10, 7)->nullable();

            // Date & schedule
            $table->date('preferred_date')->nullable();
            $table->string('schedule')->nullable();

            // Step 2: Moving details
            $table->enum('move_type', ['residential', 'office', 'storage'])->nullable();
            $table->string('origin_floor')->nullable();
            $table->boolean('origin_elevator')->default(false);
            $table->string('destination_floor')->nullable();
            $table->boolean('destination_elevator')->default(false);
            $table->boolean('packing_service')->default(false);
            $table->text('comments')->nullable();

            // Status
            $table->enum('status', [
                'pending',       // recien llegada
                'in_review',     // revisada por el equipo
                'quoted',        // ya se le envió presupuesto
                'closed',        // cliente cerró
                'cancelled'      // descartada
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moving_quotes');
    }
};
