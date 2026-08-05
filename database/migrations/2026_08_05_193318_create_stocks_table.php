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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id(); // BIGINT Unsigned Primary Key

            // Foreign Key -> pharmacies table
            $table->foreignId('pharmacy_id')
                  ->constrained('pharmacies')
                  ->onDelete('cascade');

            // Foreign Key -> medicines table
            $table->foreignId('medicine_id')
                  ->constrained('medicines')
                  ->onDelete('cascade');

            // Non-negative price up to 999,999.99
            $table->decimal('price', 8, 2)->unsigned();

            // Stock availability toggle
            $table->boolean('in_stock')->default(true);

            $table->timestamps();

            // Prevents same pharmacy from listing same medicine twice cannot pick a singular phramacy twice
            $table->unique(['pharmacy_id', 'medicine_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};