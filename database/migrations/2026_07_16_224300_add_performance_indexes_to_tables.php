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
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('order_date');
            $table->index('status');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('date');
            $table->index('status');
            $table->index('payment_status');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('category');
            $table->index('is_featured');
            $table->index('approval_status');
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index('severity');
        });

        Schema::table('service_notifications', function (Blueprint $table) {
            $table->index('read_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['order_date']);
            $table->dropIndex(['status']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['approval_status']);
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['severity']);
        });

        Schema::table('service_notifications', function (Blueprint $table) {
            $table->dropIndex(['read_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
};
