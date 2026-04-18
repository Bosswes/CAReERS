<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_info', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('section');
            $table->string('birth_place')->nullable()->after('birth_date');
            $table->text('full_address')->nullable()->after('birth_place');
        });
    }

    public function down(): void
    {
        Schema::table('student_info', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'birth_place', 'full_address']);
        });
    }
};