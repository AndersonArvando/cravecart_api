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
        Schema::create('makanan', function (Blueprint $table) {
            $table->id();
            $table->integer('kantin_id')->nullable();
            $table->string('name')->nullable();
            $table->decimal('price')->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->tinyInteger('is_ready')->default(1);
            $table->tinyInteger('enabled')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makanan');
    }
};
