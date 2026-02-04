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
        Schema::create('admins', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('display_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->uuid('role_id')->nullable()->constrained('roles','id')->nullOnDelete();
            $table->string('status')->default('active');
            $table->boolean('is_system')->default('0');
            $table->timestamp('email_verified_at')->nullable(); 
            $table->timestamp('deleted_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
