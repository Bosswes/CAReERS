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
        Schema::create('event_registrants', function (Blueprint $table) {
            $table->id('registrant_id');
            $table->unsignedBigInteger('event_id');
            $table->string('student_number');
            $table->string('qr_code')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'student_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrants');
    }
};  // ← this semicolon is mandatory