<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->string('student_number')->nullable()->unique();
            $table->string('gender')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('status')->default('Active');
        });

        DB::table('students')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($students): void {
                foreach ($students as $student) {
                    DB::table('students')
                        ->where('id', $student->id)
                        ->update(['student_number' => sprintf('STU-%06d', $student->id)]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table): void {
            $table->dropUnique(['student_number']);
            $table->dropUnique(['email']);
            $table->dropColumn(['student_number', 'gender', 'phone_number', 'email', 'status']);
        });
    }
};
