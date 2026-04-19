<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_info', function (Blueprint $table) {
            $table->string('ref1_name')->nullable()->after('elem_type');
            $table->string('ref1_position')->nullable()->after('ref1_name');
            $table->string('ref1_company')->nullable()->after('ref1_position');
            $table->string('ref1_contact')->nullable()->after('ref1_company');
            $table->string('ref2_name')->nullable()->after('ref1_contact');
            $table->string('ref2_position')->nullable()->after('ref2_name');
            $table->string('ref2_company')->nullable()->after('ref2_position');
            $table->string('ref2_contact')->nullable()->after('ref2_company');
        });
    }

    public function down(): void
    {
        Schema::table('student_info', function (Blueprint $table) {
            $table->dropColumn([
                'ref1_name','ref1_position','ref1_company','ref1_contact',
                'ref2_name','ref2_position','ref2_company','ref2_contact',
            ]);
        });
    }
};