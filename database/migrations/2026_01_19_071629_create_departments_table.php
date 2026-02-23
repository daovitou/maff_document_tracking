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
        Schema::create('departments', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid('gd_id')->nullable()->constrained('gds','id')->nullOnDelete();
            $table->string('name');
            $table->integer('order')->default(1);
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable()->constrained('admins','id')->nullOnDelete();
            $table->uuid('updated_by')->nullable()->constrained('admins','id')->nullOnDelete();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
