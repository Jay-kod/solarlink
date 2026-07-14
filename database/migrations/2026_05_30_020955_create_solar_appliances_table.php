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
        Schema::create('solar_appliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['panel', 'inverter', 'battery', 'other']);
            $table->string('brand');
            $table->string('model');
            $table->string('capacity')->nullable();
            $table->date('install_date')->nullable();
            $table->string('serial_number')->nullable();
            $table->enum('status', ['active', 'standby', 'offline'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solar_appliances');
    }
};
