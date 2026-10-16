<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentCourseSchemaMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_student_and_course_data_is_migrated_to_the_required_schema(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('fees');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('students');
        Schema::dropIfExists('courses');

        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('course_code')->nullable()->unique();
            $table->string('duration')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table): void {
            $table->id();
            $table->string('firstname');
            $table->string('secondname');
            $table->string('email')->unique();
            $table->string('phoneno');
            $table->string('course');
            $table->timestamps();
        });
        Schema::create('fees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('fee_type');
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('enrollments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
        });
        Schema::create('academic_years', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('semesters', function (Blueprint $table): void {
            $table->id();
        });
        Schema::enableForeignKeyConstraints();

        DB::table('courses')->insert([
            'name' => 'Software Development',
            'course_code' => 'SD101',
            'duration' => '4 years',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('students')->insert([
            'firstname' => 'John',
            'secondname' => 'Mwangi',
            'email' => 'john@example.test',
            'phoneno' => '0700000000',
            'course' => 'Software Development',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('students')->insert([
            'firstname' => 'Mary',
            'secondname' => 'Maina',
            'email' => 'mary@example.test',
            'phoneno' => '0711111111',
            'course' => '',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('enrollments')->insert([
            'student_id' => 2,
            'course_id' => 1,
        ]);

        $migration = require database_path('migrations/2026_10_16_090000_align_student_course_tables.php');
        $migration->up();

        $course = DB::table('courses')->first();
        $students = DB::table('students')->orderBy('id')->get();

        $this->assertSame('John', $students[0]->first_name);
        $this->assertSame('Mwangi', $students[0]->second_name);
        $this->assertSame((int) $course->id, (int) $students[0]->course_id);
        $this->assertSame((int) $course->id, (int) $students[1]->course_id);
        $this->assertSame(['id', 'name', 'created_at', 'updated_at'], Schema::getColumnListing('courses'));
        $this->assertEqualsCanonicalizing(
            ['id', 'first_name', 'second_name', 'course_id', 'created_at', 'updated_at'],
            Schema::getColumnListing('students')
        );
        $this->assertFalse(Schema::hasTable('enrollments'));
        $this->assertFalse(Schema::hasTable('academic_years'));
        $this->assertFalse(Schema::hasTable('semesters'));
        $this->assertSame(
            ['id', 'student_id', 'amount', 'fee_type', 'status', 'created_at', 'updated_at'],
            Schema::getColumnListing('fees')
        );
    }
}
