<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
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
}