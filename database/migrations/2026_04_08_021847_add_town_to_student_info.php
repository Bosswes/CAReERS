<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_info', function (Blueprint $table) {
            $table->string('town')->nullable()->after('section');
            $table->longText('profile_photo')->nullable()->after('town');
            $table->string('shs_school')->nullable()->after('town');
            $table->string('shs_year_grad')->nullable()->after('shs_school');
            $table->string('shs_type')->nullable()->after('shs_year_grad');
            $table->string('hs_school')->nullable()->after('shs_type');
            $table->string('hs_year_grad')->nullable()->after('hs_school');
            $table->string('hs_type')->nullable()->after('hs_year_grad');
            $table->string('elem_school')->nullable()->after('hs_type');
            $table->string('elem_year_grad')->nullable()->after('elem_school');
            $table->string('elem_type')->nullable()->after('elem_year_grad');
        });
    }

    public function down(): void
    {
        Schema::table('student_info', function (Blueprint $table) {
            $table->dropColumn([
                'town',
                'profile_photo',
                'shs_school', 'shs_year_grad', 'shs_type',
                'hs_school', 'hs_year_grad', 'hs_type',
                'elem_school', 'elem_year_grad', 'elem_type'
            ]);
        });
    }
};