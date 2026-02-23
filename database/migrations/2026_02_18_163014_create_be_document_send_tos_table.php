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
        Schema::create('be_document_send_tos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->boolean('is_personel')->nullable()->default(false);
            $table->uuid('be_document_id')->nullable()->constrained("be_documents", "id")->cascadeOnDelete("set null");
            $table->string('status')->default('កំពុងរងចាំ');
            $table->boolean('to_gd')->default(true);
            $table->uuid("gd_id")->nullable()->constrained("gds", "id")->cascadeOnDelete("set null");
            $table->uuid("department_id")->nullable()->constrained("departments", "id")->cascadeOnDelete("set null");
            $table->uuid("personel_id")->nullable()->constrained("personels", "id")->cascadeOnDelete("set null");
            $table->timestamp("respect_at")->nullable();
            $table->timestamp("send_at")->nullable();
            $table->timestamp("cancel_at")->nullable();
            $table->timestamp("return_at")->nullable();
            $table->text("send_note")->nullable();
            $table->text("cancel_note")->nullable();
            $table->text("return_note")->nullable();
            $table->string('cancel_file')->nullable();
            $table->string('return_file')->nullable();
            $table->uuid("for_gd_id")->nullable()->constrained("gds", "id")->cascadeOnDelete("set null");
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
        Schema::dropIfExists('be_document_send_tos');
    }
};
