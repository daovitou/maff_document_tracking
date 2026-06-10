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
        Schema::create('doc_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('document_id');
            $table->string('document_type')->nullable();
            $table->string('document_file')->nullable();
            $table->text('original_name')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->uuid('created_by')->nullable()->constrained('admins', 'id')->nullOnDelete();
            $table->uuid('updated_by')->nullable()->constrained('admins', 'id')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_files');
    }
};
