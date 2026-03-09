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
    Schema::create('stations', function (Blueprint $table) {
        $table->id();
        $table->string('name'); 
        $table->decimal('latitude', 10, 8);
        $table->decimal('longitude', 11, 8);
        $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
        $table->string('connector_type'); 
        $table->float('power_kw');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
