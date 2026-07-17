<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->unsignedInteger('lead_time_days')->default(3);
            $table->text('notes')->nullable();
            $table->enum('status', ['submitted', 'selected', 'rejected'])->default('submitted');
            $table->timestamps();
            $table->unique(['procurement_request_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_quotes');
    }
};
