<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_fee_management(): void
    {
        $this->get(route('fees.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_record_and_view_a_fee(): void
    {
        $user = User::factory()->create();
        $course = Course::create(['name' => 'Computer Science']);
        $student = Student::create([
            'first_name' => 'Amina',
            'second_name' => 'Otieno',
            'course_id' => $course->id,
        ]);

        $this->actingAs($user)
            ->get(route('fees.index'))
            ->assertOk()
            ->assertSee('Record and review student fee charges.')
            ->assertSee(route('fees.create'), false);

        $this->get(route('fees.create'))
            ->assertOk()
            ->assertSee('Amina Otieno');

        $this->post(route('fees.store'), [
            'student_id' => $student->id,
            'amount' => '12500.50',
            'fee_type' => 'Tuition',
            'status' => 'Paid',
        ])->assertRedirect(route('fees.index'));

        $this->assertDatabaseHas('fees', [
            'student_id' => $student->id,
            'amount' => '12500.50',
            'fee_type' => 'Tuition',
            'status' => 'Paid',
        ]);

        $this->get(route('fees.index'))
            ->assertOk()
            ->assertSeeText('Amina Otieno')
            ->assertSeeText('Tuition')
            ->assertSeeText('12,500.50')
            ->assertSeeText('Paid');
    }

    public function test_fee_creation_validates_student_amount_and_status(): void
    {
        $this->actingAs(User::factory()->create())
            ->from(route('fees.create'))
            ->post(route('fees.store'), [
                'student_id' => 999,
                'amount' => 0,
                'fee_type' => '',
                'status' => 'Unknown',
            ])
            ->assertSessionHasErrors(['student_id', 'amount', 'fee_type', 'status']);

        $this->assertDatabaseCount('fees', 0);
    }
}
