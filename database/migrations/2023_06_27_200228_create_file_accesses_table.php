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
        Schema::create('file_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('author|co-author');
            $table->foreignId('fk_file')->constrained('files')->cascadeOnDelete();
            $table->foreignId('fk_author')->constrained('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_accesses');
    }
};
