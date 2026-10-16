<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_the_dashboard_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_required_tables_have_the_expected_columns(): void
    {
        $this->assertSame(
            ['id', 'name', 'created_at', 'updated_at'],
            Schema::getColumnListing('courses')
        );
        $this->assertSame(
            ['id', 'first_name', 'second_name', 'course_id', 'created_at', 'updated_at'],
            Schema::getColumnListing('students')
        );
        $this->assertSame(
            ['id', 'student_id', 'amount', 'fee_type', 'status', 'created_at', 'updated_at'],
            Schema::getColumnListing('fees')
        );
        $this->assertFalse(Schema::hasTable('enrollments'));
        $this->assertFalse(Schema::hasTable('academic_years'));
        $this->assertFalse(Schema::hasTable('semesters'));
    }

    public function test_authenticated_users_can_access_the_dashboard_without_verified_email(): void
    {
        $user = User::factory()->unverified()->create(['name' => 'Dashboard Student']);
        $course = Course::create(['name' => 'Computer Science']);
        Course::create(['name' => 'Business']);
        Course::create(['name' => 'History']);
        $student = Student::create([
            'first_name' => 'Amina',
            'second_name' => 'Otieno',
            'course_id' => $course->id,
        ]);
        Fee::create([
            'student_id' => $student->id,
            'amount' => 20000,
            'fee_type' => 'Tuition',
            'status' => 'Paid',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Dashboard Student')
            ->assertSeeText('Welcome to the Student Management System.')
            ->assertSeeInOrder([
                'Total Students',
                '>1</h3>',
                'Total Courses',
                '>3</h3>',
                'Fee Records',
                '>1</h3>',
            ], false)
            ->assertSee('class="nav-link active"', false);
    }

    public function test_sidebar_links_open_authenticated_placeholder_pages(): void
    {
        $user = User::factory()->create();

        foreach ([
            'students.index' => 'Students',
            'courses.index' => 'Courses',
            'fees.index' => 'Fees',
            'payments.index' => 'Payments',
            'reports.index' => 'Reports',
            'settings' => 'Settings',
        ] as $route => $title) {
            $this->actingAs($user)
                ->get(route($route))
                ->assertOk()
                ->assertSeeText($title);
        }
    }

    public function test_settings_page_links_to_profile_management(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('settings'))
            ->assertOk()
            ->assertSee(route('profile.edit'), false);
    }

    public function test_authenticated_user_can_register_a_student_for_a_course(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['name' => 'Computer Science']);

        $response = $this->actingAs($user)
            ->post(route('students.store'), [
                'first_name' => 'Amina',
                'second_name' => 'Otieno',
                'course_id' => $course->id,
            ]);

        $student = Student::where('first_name', 'Amina')->firstOrFail();

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'Amina',
            'second_name' => 'Otieno',
            'course_id' => $course->id,
        ]);
        $this->assertDatabaseHas('courses', ['name' => 'Computer Science']);
    }

    public function test_student_registration_requires_valid_student_and_course_details(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('students.create'))
            ->post(route('students.store'), [
                'first_name' => '',
                'second_name' => '',
                'course_id' => 999,
            ])
            ->assertSessionHasErrors(['first_name', 'second_name', 'course_id']);

        $this->assertDatabaseCount('students', 0);
    }

    public function test_dashboard_navigation_has_working_menu_links_and_account_actions(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('students.index'), false)
            ->assertSee(route('courses.index'), false)
            ->assertSee(route('fees.index'), false)
            ->assertSee(route('payments.index'), false)
            ->assertSee(route('reports.index'), false)
            ->assertSee(route('profile.edit'), false)
            ->assertSee(route('settings'), false)
            ->assertSee(route('logout'), false)
            ->assertSee('Toggle sidebar')
            ->assertDontSee('href="#"', false);
    }
}
