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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('route_map')->nullable();
            $table->text('description')->nullable();
            $table->decimal('distance', 8, 2)->nullable();
            $table->decimal('duration', 8, 2)->nullable();
            $table->unsignedInteger('totalScore')->default(0);
            $table->unsignedInteger('countScore')->default(0);
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->foreignId('terrain_id')->constrained()->onDelete('cascade');
            $table->foreignId('difficulty_id')->constrained()->onDelete('cascade');
            $table->foreignId('landscape_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
