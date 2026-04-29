<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('student_number');
            $table->string('type'); // 'job' or 'announcement'
            $table->string('title');
            $table->text('message');
            $table->unsignedBigInteger('reference_id')->nullable(); // job_id or announcement_id
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('student_notifications');
    }
};