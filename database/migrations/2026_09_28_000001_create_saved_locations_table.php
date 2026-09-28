<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label', 80);
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('is_primary')->default(false);
            $table->boolean('share_with_users')->default(false);
            $table->timestamps();
            $table->index(['share_with_users', 'latitude', 'longitude']);
        });

        Schema::create('live_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_sharing')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->index(['is_sharing', 'last_seen_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_locations');
        Schema::dropIfExists('saved_locations');
    }
};
