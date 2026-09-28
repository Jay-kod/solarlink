<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('users', 'location')) {
                $table->string('location')->nullable()->after('longitude');
            }
        });

        Schema::table('technician_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('technician_profiles', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('cert_file_path');
            }
            if (!Schema::hasColumn('technician_profiles', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('technician_profiles', 'service_radius')) {
                $table->decimal('service_radius', 8, 2)->default(25.00)->after('longitude');
            }
            if (!Schema::hasColumn('technician_profiles', 'verification_status')) {
                $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->after('approval_status');
            }
            if (!Schema::hasColumn('technician_profiles', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'verified', 'suspended'])->default('pending')->after('status');
            }
        });

        Schema::table('vendor_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_profiles', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('company_address');
            }
            if (!Schema::hasColumn('vendor_profiles', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('vendor_profiles', 'verification_status')) {
                $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->after('approval_status');
            }
            if (!Schema::hasColumn('vendor_profiles', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'verified', 'suspended'])->default('pending')->after('vat_number');
            }
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenance_requests', 'fault_type')) {
                $table->string('fault_type')->nullable()->after('technician_profile_id');
            }
            if (!Schema::hasColumn('maintenance_requests', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->after('fault_type');
            }
            if (!Schema::hasColumn('maintenance_requests', 'location')) {
                $table->string('location')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('maintenance_requests', 'scheduled_date')) {
                $table->date('scheduled_date')->nullable()->after('location');
            }
            if (!Schema::hasColumn('maintenance_requests', 'scheduled_time')) {
                $table->string('scheduled_time')->nullable()->after('scheduled_date');
            }
            if (!Schema::hasColumn('maintenance_requests', 'technician_notes')) {
                $table->text('technician_notes')->nullable()->after('scheduled_time');
            }
            if (!Schema::hasColumn('maintenance_requests', 'customer_notes')) {
                $table->text('customer_notes')->nullable()->after('technician_notes');
            }
            if (!Schema::hasColumn('maintenance_requests', 'cost')) {
                $table->decimal('cost', 12, 2)->nullable()->after('customer_notes');
            }
            if (!Schema::hasColumn('maintenance_requests', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('cost');
            }
            if (!Schema::hasColumn('maintenance_requests', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('started_at');
            }
            if (!Schema::hasColumn('maintenance_requests', 'status')) {
                $table->enum('status', ['reported', 'pending', 'assigned', 'accepted', 'scheduled', 'in_progress', 'completed', 'cancelled'])->default('reported');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('products', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('users', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('users', 'location')) {
                $table->dropColumn('location');
            }
        });

        Schema::table('technician_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('technician_profiles', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('technician_profiles', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('technician_profiles', 'service_radius')) {
                $table->dropColumn('service_radius');
            }
            if (Schema::hasColumn('technician_profiles', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
        });

        Schema::table('vendor_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_profiles', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('vendor_profiles', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('vendor_profiles', 'verification_status')) {
                $table->dropColumn('verification_status');
            }
        });

        Schema::table('maintenance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_requests', 'fault_type')) {
                $table->dropColumn('fault_type');
            }
            if (Schema::hasColumn('maintenance_requests', 'priority')) {
                $table->dropColumn('priority');
            }
            if (Schema::hasColumn('maintenance_requests', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('maintenance_requests', 'scheduled_date')) {
                $table->dropColumn('scheduled_date');
            }
            if (Schema::hasColumn('maintenance_requests', 'scheduled_time')) {
                $table->dropColumn('scheduled_time');
            }
            if (Schema::hasColumn('maintenance_requests', 'technician_notes')) {
                $table->dropColumn('technician_notes');
            }
            if (Schema::hasColumn('maintenance_requests', 'customer_notes')) {
                $table->dropColumn('customer_notes');
            }
            if (Schema::hasColumn('maintenance_requests', 'cost')) {
                $table->dropColumn('cost');
            }
            if (Schema::hasColumn('maintenance_requests', 'started_at')) {
                $table->dropColumn('started_at');
            }
            if (Schema::hasColumn('maintenance_requests', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'vendor_id')) {
                $table->dropConstrainedForeignId('vendor_id');
            }
            if (Schema::hasColumn('products', 'approval_status')) {
                $table->dropColumn('approval_status');
            }
        });
    }
};
