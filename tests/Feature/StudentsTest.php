<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_and_edit_student_information(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['name' => 'Computer Science']);
        $otherCourse = Course::create(['name' => 'Business']);
        $student = Student::create([
            'first_name' => 'Amina',
            'second_name' => 'Otieno',
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->get(route('students.index'))
            ->assertOk()
            ->assertSee(route('students.show', $student), false)
            ->assertSee(route('students.edit', $student), false)
            ->assertSee(route('students.destroy', $student), false);

        $this->get(route('students.show', $student))
            ->assertOk()
            ->assertSeeText('Amina')
            ->assertSeeText('Otieno')
            ->assertSeeText($student->student_number)
            ->assertSeeText('Active')
            ->assertSeeText('Computer Science')
            ->assertSeeText('Fee records');

        $this->get(route('students.edit', $student))
            ->assertOk()
            ->assertSee('selected', false);

        $this->put(route('students.update', $student), [
            'first_name' => 'Amara',
            'second_name' => 'Otieno',
            'course_id' => $otherCourse->id,
            'gender' => 'Female',
            'phone_number' => '0712345678',
            'email' => 'amara@example.test',
            'status' => 'Inactive',
        ])->assertRedirect(route('students.show', $student));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'first_name' => 'Amara',
            'second_name' => 'Otieno',
            'course_id' => $otherCourse->id,
            'gender' => 'Female',
            'phone_number' => '0712345678',
            'email' => 'amara@example.test',
            'status' => 'Inactive',
        ]);
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'student_number' => $student->student_number,
            'gender' => 'Female',
            'phone_number' => '0712345678',
            'email' => 'amara@example.test',
            'status' => 'Inactive',
        ]);
        $this->get(route('students.index'))
            ->assertOk()
            ->assertSeeText($student->student_number)
            ->assertSeeText('Female')
            ->assertSeeText('0712345678')
            ->assertSeeText('amara@example.test')
            ->assertSeeText('Inactive');
    }

    public function test_student_registration_generates_student_number_and_saves_contact_details(): void
    {
        $course = Course::create(['name' => 'Computer Science']);

        $this->actingAs(User::factory()->create())
            ->post(route('students.store'), [
                'first_name' => 'Amina',
                'second_name' => 'Otieno',
                'course_id' => $course->id,
                'gender' => 'Female',
                'phone_number' => '0712345678',
                'email' => 'amina@example.test',
                'status' => 'Graduated',
            ])
            ->assertRedirect(route('students.index'));

        $student = Student::where('email', 'amina@example.test')->firstOrFail();
        $this->assertSame(sprintf('STU-%06d', $student->id), $student->student_number);
        $this->assertSame('Graduated', $student->status);
    }

    public function test_student_without_fees_can_be_deleted(): void
    {
        $student = Student::create([
            'first_name' => 'Amina',
            'second_name' => 'Otieno',
            'course_id' => Course::create(['name' => 'Computer Science'])->id,
        ]);

        $this->actingAs(User::factory()->create())
            ->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('status', 'Student deleted successfully.');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_student_with_fee_records_cannot_be_deleted(): void
    {
        $student = Student::create([
            'first_name' => 'Amina',
            'second_name' => 'Otieno',
            'course_id' => Course::create(['name' => 'Computer Science'])->id,
        ]);
        Fee::create([
            'student_id' => $student->id,
            'amount' => 1000,
            'fee_type' => 'Tuition',
            'status' => 'Paid',
        ]);

        $this->actingAs(User::factory()->create())
            ->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('students', ['id' => $student->id]);
        $this->assertDatabaseHas('fees', ['student_id' => $student->id]);
    }
}
