<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Payment;
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
            'reference' => 'FEE-2026-001',
            'status' => 'Paid',
        ])->assertRedirect(route('fees.index'));

        $this->assertDatabaseHas('fees', [
            'student_id' => $student->id,
            'amount' => '12500.50',
            'fee_type' => 'Tuition',
            'reference' => 'FEE-2026-001',
            'status' => 'Paid',
        ]);

        $this->get(route('fees.index'))
            ->assertOk()
            ->assertSeeText('Amina Otieno')
            ->assertSeeText('Tuition')
            ->assertSeeText('FEE-2026-001')
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

    public function test_authenticated_user_can_edit_update_and_delete_a_fee(): void
    {
        $user = User::factory()->create();
        $student = $this->createStudent('Amina', 'Otieno');
        $fee = Fee::create([
            'student_id' => $student->id,
            'amount' => '12500.50',
            'fee_type' => 'Tuition',
            'status' => 'Pending',
        ]);

        $this->actingAs($user)
            ->get(route('fees.edit', $fee))
            ->assertOk()
            ->assertSee('Edit fee')
            ->assertSee('Amina Otieno')
            ->assertSee('12500.50');

        $this->put(route('fees.update', $fee), [
            'student_id' => $student->id,
            'amount' => '15000.00',
            'fee_type' => 'Registration',
            'reference' => 'REG-2026-002',
            'status' => 'Partial',
        ])->assertRedirect(route('fees.index'));

        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'amount' => '15000.00',
            'fee_type' => 'Registration',
            'reference' => 'REG-2026-002',
            'status' => 'Partial',
        ]);

        $this->delete(route('fees.destroy', $fee))
            ->assertRedirect(route('fees.index'))
            ->assertSessionHas('status', 'Fee record deleted successfully.');
        $this->assertDatabaseMissing('fees', ['id' => $fee->id]);
    }

    public function test_fee_cannot_be_deleted_when_it_has_payments(): void
    {
        $fee = $this->createFee();
        Payment::create([
            'fee_id' => $fee->id,
            'amount' => '100.00',
            'method' => 'Cash',
            'status' => 'Completed',
            'paid_at' => now(),
        ]);

        $this->actingAs(User::factory()->create())
            ->delete(route('fees.destroy', $fee))
            ->assertRedirect(route('fees.index'))
            ->assertSessionHas('error', 'This fee has payment records and cannot be deleted.');

        $this->assertDatabaseHas('fees', ['id' => $fee->id]);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_fee_update_preserves_recorded_payment_integrity(): void
    {
        $fee = $this->createFee();
        Payment::create([
            'fee_id' => $fee->id,
            'amount' => '400.00',
            'method' => 'Cash',
            'status' => 'Completed',
            'paid_at' => now(),
        ]);
        $otherStudent = $this->createStudent('Brian', 'Kariuki');

        $this->actingAs(User::factory()->create())
            ->from(route('fees.edit', $fee))
            ->put(route('fees.update', $fee), [
                'student_id' => $otherStudent->id,
                'amount' => '300.00',
                'fee_type' => 'Tuition',
                'status' => 'Pending',
            ])
            ->assertSessionHasErrors(['student_id', 'amount']);

        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'student_id' => $fee->student_id,
            'amount' => '1000.00',
        ]);
    }

    private function createFee(): Fee
    {
        $student = $this->createStudent();

        return Fee::create([
            'student_id' => $student->id,
            'amount' => '1000.00',
            'fee_type' => 'Tuition',
            'status' => 'Pending',
        ]);
    }

    private function createStudent(string $firstName = 'Amina', string $secondName = 'Otieno'): Student
    {
        $course = Course::create(['name' => 'Course '.$firstName.$secondName]);

        return Student::create([
            'first_name' => $firstName,
            'second_name' => $secondName,
            'course_id' => $course->id,
        ]);
    }
}
