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
        Schema::create('docs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code');
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('original_file')->nullable();
            $table->string('status')->default('pending');
            $table->uuid("department_id")->nullable()->constrained("departments","id")->cascadeOnDelete("set null");
            $table->timestamp("post_at")->nullable();
            $table->uuid("post_by")->nullable()->constrained("users","id")->cascadeOnDelete("set null");
            $table->text('post_note')->nullable();
            $table->timestamp("modified_at")->nullable();
            $table->uuid("modified_by")->nullable()->constrained("users","id")->cascadeOnDelete("set null");
            $table->string("speecher_by")->nullable();
            $table->string('return_file')->nullable();
            $table->timestamp("return_at")->nullable();
            $table->uuid("return_by")->nullable()->constrained("users","id")->cascadeOnDelete("set null");
            $table->text('return_note')->nullable();
            $table->timestamp("deleted_at")->nullable();
            $table->uuid("deleted_by")->nullable()->constrained("users","id")->cascadeOnDelete("set null");
            $table->text('cancel_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docs');
    }
};
