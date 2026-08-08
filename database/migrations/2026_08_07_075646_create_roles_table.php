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
        // 1. Create the roles table with slug
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // e.g. "Super Administrator"
            $table->string('slug')->unique();            // e.g. "super-admin" (Unique Identifier)
            $table->timestamps();
        });

    // Add role_id foreign key column to the users table
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('role_id')->nullable()->after('id')->constrained()->onDelete('set null');
    });
}

public function down(): void
{
    // 1. Remove foreign key and column from users table first
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['role_id']);
        $table->dropColumn('role_id');
    });

    // 2. Drop the roles table
    Schema::dropIfExists('roles');
}

};
