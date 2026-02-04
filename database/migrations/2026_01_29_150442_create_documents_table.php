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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code');
            $table->timestamp('article_at')->nullable();
            $table->string('article');
            $table->string('source')->nullable();
            $table->string('description')->nullable();
            $table->string('document_file')->nullable();
            $table->string('status')->default('កំពុងរងចាំ');
            $table->boolean('to_gd')->default(true);
            $table->uuid("gd_id")->nullable()->constrained("gds", "id")->cascadeOnDelete("set null");
            $table->uuid("department_id")->nullable()->constrained("departments", "id")->cascadeOnDelete("set null");
            $table->uuid("personel_id")->nullable()->constrained("personels", "id")->cascadeOnDelete("set null");
            $table->timestamp("send_at")->nullable();
            $table->timestamp("cancel_at")->nullable();
            $table->timestamp("return_at")->nullable();
            $table->text("send_note")->nullable();
            $table->text("cancel_note")->nullable();
            $table->text("return_note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
