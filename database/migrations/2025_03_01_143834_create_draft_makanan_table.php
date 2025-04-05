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
        Schema::create('draft_makanan', function (Blueprint $table) {
            $table->id();
            $table->integer('mahasiswa_id')->nullable();
            $table->integer('kantin_id')->nullable();
            $table->integer('makanan_id')->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_makanan');
    }
};
