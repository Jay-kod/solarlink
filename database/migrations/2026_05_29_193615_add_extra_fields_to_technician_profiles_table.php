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
        Schema::table('technician_profiles', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('user_id');
            $table->decimal('rating', 3, 2)->default(0.00)->after('cert_name');
            $table->integer('review_count')->default(0)->after('rating');
            $table->string('distance')->nullable()->after('review_count');
            $table->json('skills')->nullable()->after('distance');
            $table->enum('status', ['online', 'offline', 'busy'])->default('offline')->after('skills');
            $table->decimal('lat', 10, 7)->nullable()->after('status');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            $table->string('eta')->nullable()->after('lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technician_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'rating',
                'review_count',
                'distance',
                'skills',
                'status',
                'lat',
                'lng',
                'eta'
            ]);
        });
    }
};
