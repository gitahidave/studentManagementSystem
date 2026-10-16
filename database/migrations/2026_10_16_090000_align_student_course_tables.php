<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses') || ! Schema::hasTable('students')) {
            return;
        }

        $legacyCourseColumns = array_values(array_intersect(
            ['course_code', 'duration', 'status'],
            Schema::getColumnListing('courses')
        ));

        if ($legacyCourseColumns !== []) {
            $courseCodeIndex = collect(Schema::getIndexes('courses'))
                ->first(fn (array $index): bool => ($index['columns'] ?? []) === ['course_code']
                    && ($index['unique'] ?? false));

            Schema::table('courses', function (Blueprint $table) use ($courseCodeIndex, $legacyCourseColumns): void {
                if ($courseCodeIndex !== null) {
                    $table->dropUnique($courseCodeIndex['name']);
                }

                $table->dropColumn($legacyCourseColumns);
            });
        }

        if (Schema::hasColumn('students', 'firstname')) {
            Schema::table('students', function (Blueprint $table): void {
                $table->renameColumn('firstname', 'first_name');
            });
        }

        if (Schema::hasColumn('students', 'secondname')) {
            Schema::table('students', function (Blueprint $table): void {
                $table->renameColumn('secondname', 'second_name');
            });
        }

        if (! Schema::hasColumn('students', 'course_id')) {
            Schema::table('students', function (Blueprint $table): void {
                $table->unsignedBigInteger('course_id')->nullable();
            });
        }

        $hasLegacyCourseColumn = Schema::hasColumn('students', 'course');
        $students = DB::table('students')
            ->select($hasLegacyCourseColumn ? ['id', 'course'] : ['id', 'course_id'])
            ->orderBy('id');

        $unassignedCourseId = null;
        $students->chunkById(100, function ($rows) use ($hasLegacyCourseColumn, &$unassignedCourseId): void {
            foreach ($rows as $row) {
                $courseName = $hasLegacyCourseColumn ? trim((string) $row->course) : '';
                $courseId = $courseName !== ''
                    ? $this->findOrCreateCourse($courseName)
                    : (isset($row->course_id) && DB::table('courses')->where('id', $row->course_id)->exists()
                        ? (int) $row->course_id
                        : null);

                if ($courseId === null && Schema::hasTable('enrollments')) {
                    $enrolledCourseId = DB::table('enrollments')
                        ->where('student_id', $row->id)
                        ->value('course_id');

                    if ($enrolledCourseId !== null
                        && DB::table('courses')->where('id', $enrolledCourseId)->exists()) {
                        $courseId = (int) $enrolledCourseId;
                    }
                }

                if ($courseId === null) {
                    $unassignedCourseId ??= $this->findOrCreateCourse('Unassigned');
                    $courseId = $unassignedCourseId;
                }

                DB::table('students')->where('id', $row->id)->update(['course_id' => $courseId]);
            }
        });

        $courseColumn = collect(Schema::getColumns('students'))->firstWhere('name', 'course_id');
        if ($courseColumn['nullable'] ?? false) {
            Schema::table('students', function (Blueprint $table): void {
                $table->unsignedBigInteger('course_id')->nullable(false)->change();
            });
        }

        $courseForeignKey = collect(Schema::getForeignKeys('students'))
            ->first(fn (array $foreignKey): bool => ($foreignKey['columns'] ?? []) === ['course_id']
                && ($foreignKey['foreign_table'] ?? null) === 'courses');
        if ($courseForeignKey === null) {
            Schema::table('students', function (Blueprint $table): void {
                $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            });
        }

        $legacyStudentColumns = array_values(array_intersect(
            ['email', 'phoneno', 'course'],
            Schema::getColumnListing('students')
        ));
        if ($legacyStudentColumns !== []) {
            $emailIndex = collect(Schema::getIndexes('students'))
                ->first(fn (array $index): bool => ($index['columns'] ?? []) === ['email']
                    && ($index['unique'] ?? false));

            Schema::table('students', function (Blueprint $table) use ($emailIndex, $legacyStudentColumns): void {
                if ($emailIndex !== null) {
                    $table->dropUnique($emailIndex['name']);
                }

                $table->dropColumn($legacyStudentColumns);
            });
        }

        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('semesters');
    }

    public function down(): void
    {
        if (! Schema::hasTable('students') || ! Schema::hasColumn('students', 'course_id')) {
            return;
        }

        Schema::table('students', function (Blueprint $table): void {
            $table->dropForeign(['course_id']);
            $table->string('email')->nullable();
            $table->string('phoneno')->nullable();
            $table->string('course')->nullable();
        });

        DB::table('students')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->update(['students.course' => DB::raw('courses.name')]);

        Schema::table('students', function (Blueprint $table): void {
            $table->dropColumn('course_id');
        });

        if (Schema::hasColumn('students', 'first_name')) {
            Schema::table('students', function (Blueprint $table): void {
                $table->renameColumn('first_name', 'firstname');
            });
        }

        if (Schema::hasColumn('students', 'second_name')) {
            Schema::table('students', function (Blueprint $table): void {
                $table->renameColumn('second_name', 'secondname');
            });
        }

        if (Schema::hasColumn('students', 'email')) {
            Schema::table('students', function (Blueprint $table): void {
                $table->unique('email');
            });
        }

        $legacyCourseColumns = array_values(array_diff(
            ['course_code', 'duration', 'status'],
            Schema::getColumnListing('courses')
        ));

        if ($legacyCourseColumns !== []) {
            Schema::table('courses', function (Blueprint $table) use ($legacyCourseColumns): void {
                if (in_array('course_code', $legacyCourseColumns, true)) {
                    $table->string('course_code')->nullable()->unique();
                }

                if (in_array('duration', $legacyCourseColumns, true)) {
                    $table->string('duration')->nullable();
                }

                if (in_array('status', $legacyCourseColumns, true)) {
                    $table->string('status')->default('active');
                }
            });
        }
    }

    private function findOrCreateCourse(string $name): int
    {
        $course = DB::table('courses')->where('name', $name)->first();

        if ($course !== null) {
            return (int) $course->id;
        }

        return (int) DB::table('courses')->insertGetId([
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
