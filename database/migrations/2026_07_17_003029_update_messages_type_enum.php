<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE messages MODIFY type ENUM('text', 'image', 'file', 'system') NOT NULL DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Safe down migration: first, turn any 'system' types back to 'text'
        DB::table('messages')->where('type', 'system')->update(['type' => 'text']);
        DB::statement("ALTER TABLE messages MODIFY type ENUM('text', 'image', 'file') NOT NULL DEFAULT 'text'");
    }
};
