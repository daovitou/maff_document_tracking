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
        Schema::create('be_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
             $table->string('code');
            $table->timestamp('article_at')->nullable();
            $table->string('article');
            $table->string('source')->nullable();
            $table->text('note')->nullable();
            $table->string('document_file')->nullable();
            $table->boolean('disable')->default(false);
            $table->uuid("for_gd_id")->nullable()->constrained("gds", "id")->cascadeOnDelete("set null");
            $table->timestamp('deleted_at')->nullable();
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
        Schema::dropIfExists('be_documents');
    }
};
