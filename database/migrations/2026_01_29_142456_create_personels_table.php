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
        Schema::create('personels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('order')->default(1);
            $table->string('note')->nullable();
            $table->timestamp("deleted_at")->nullable();
            $table->uuid('created_by')->nullable()->constrained('admins','id')->nullOnDelete();
            $table->uuid('updated_by')->nullable()->constrained('admins','id')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personels');
    }
};
