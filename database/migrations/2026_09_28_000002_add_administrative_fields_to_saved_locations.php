<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saved_locations', function (Blueprint $table) {
            $table->string('state')->nullable()->after('address');
            $table->string('local_government')->nullable()->after('state');
            $table->string('share_detail', 20)->default('street')->after('share_with_users');
        });
    }

    public function down(): void
    {
        Schema::table('saved_locations', function (Blueprint $table) {
            $table->dropColumn(['state', 'local_government', 'share_detail']);
        });
    }
};
