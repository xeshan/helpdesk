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
        Schema::create('tickets', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('subject');
            $table->text('body');
            $table->enum('status', ['open','in_progress','resolved','closed'])->default('open');
            $table->string('category')->nullable();
            $table->text('explanation')->nullable();
            $table->decimal('confidence', 3, 2)->nullable(); // 0.00 - 1.00
            $table->text('note')->nullable();
            $table->enum('category_source', ['ai','manual'])->default('ai'); // track manual overrides
            $table->timestamps();
            $table->index(['status','category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
