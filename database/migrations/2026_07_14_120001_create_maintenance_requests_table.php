<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('solar_appliance_id')->nullable()->constrained('solar_appliances')->nullOnDelete();
            $table->foreignId('technician_profile_id')->nullable()->constrained('technician_profiles')->nullOnDelete();
            $table->string('issue_type');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('description');
            $table->string('location_address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['open', 'assigned', 'in_progress', 'resolved', 'paid'])->default('open');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->decimal('estimated_cost', 12, 2)->default(0);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};
