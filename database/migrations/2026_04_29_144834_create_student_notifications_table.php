<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Huwag gumawa ng bago — handled na ng 2026_04_29_000000 migration
        // Dagdag lang ng missing columns kung incomplete ang table
        if (Schema::hasTable('student_notifications')) {
            Schema::table('student_notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('student_notifications', 'student_number')) {
                    $table->string('student_number')->after('id');
                }
                if (!Schema::hasColumn('student_notifications', 'type')) {
                    $table->string('type')->after('student_number');
                }
                if (!Schema::hasColumn('student_notifications', 'title')) {
                    $table->string('title')->after('type');
                }
                if (!Schema::hasColumn('student_notifications', 'message')) {
                    $table->text('message')->after('title');
                }
                if (!Schema::hasColumn('student_notifications', 'reference_id')) {
                    $table->unsignedBigInteger('reference_id')->nullable()->after('message');
                }
                if (!Schema::hasColumn('student_notifications', 'is_read')) {
                    $table->boolean('is_read')->default(false)->after('reference_id');
                }
            });
        }
    }

    public function down(): void
    {
        // Do nothing — handled ng ibang migration
    }
};