<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_course_management(): void
    {
        $this->get(route('courses.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_update_and_delete_courses(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('courses.index'))
            ->assertOk()
            ->assertSee('Manage courses offered by the institution.')
            ->assertSee(route('courses.create'), false);

        $this->post(route('courses.store'), [
            'course_code' => 'CS101',
            'course_name' => 'Computer Science',
            'duration' => '4 years',
            'status' => 'active',
        ])->assertRedirect(route('courses.index'));

        $course = Course::where('course_code', 'CS101')->firstOrFail();

        $this->assertDatabaseHas('courses', [
            'name' => 'Computer Science',
            'course_code' => 'CS101',
            'duration' => '4 years',
            'status' => 'active',
        ]);

        $this->put(route('courses.update', $course), [
            'course_code' => 'CS102',
            'course_name' => 'Applied Computer Science',
            'duration' => '3 years',
            'status' => 'inactive',
        ])->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Applied Computer Science',
            'course_code' => 'CS102',
            'duration' => '3 years',
            'status' => 'inactive',
        ]);

        $this->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_course_with_enrollments_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $course = Course::create([
            'name' => 'Computer Science',
            'course_code' => 'CS101',
            'status' => 'active',
        ]);
        $student = Student::create([
            'firstname' => 'Amina',
            'secondname' => 'Otieno',
            'email' => 'amina@example.test',
            'phoneno' => '+254700000000',
            'course' => $course->name,
        ]);
        $semester = Semester::create(['name' => 'Semester 1']);
        $academicYear = AcademicYear::create(['name' => '2026/2027']);
        $student->enrollments()->create([
            'course_id' => $course->id,
            'semester_id' => $semester->id,
            'academic_year_id' => $academicYear->id,
        ]);

        $this->actingAs($user)
            ->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }
}
