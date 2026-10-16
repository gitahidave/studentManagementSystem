<?php

namespace Tests\Feature;

use App\Models\Course;
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
            'name' => 'Computer Science',
        ])->assertRedirect(route('courses.index'));

        $course = Course::where('name', 'Computer Science')->firstOrFail();

        $this->assertDatabaseHas('courses', [
            'name' => 'Computer Science',
        ]);

        $this->put(route('courses.update', $course), [
            'name' => 'Applied Computer Science',
        ])->assertRedirect(route('courses.index'));

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Applied Computer Science',
        ]);

        $this->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'));

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_course_with_students_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['name' => 'Computer Science']);
        Student::create([
            'first_name' => 'Amina',
            'second_name' => 'Otieno',
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }
}
