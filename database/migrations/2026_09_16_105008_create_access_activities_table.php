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
        Schema::create('access_activities', function (Blueprint $table) {
            $table->id();

            // User who performed the action
            $table->foreignId('actor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Type of action
            $table->string('action');

            // Target information
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();

            // Human-readable description
            $table->text('description');

            // Optional additional information
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['target_type', 'target_id']);
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_activities');
    }
};