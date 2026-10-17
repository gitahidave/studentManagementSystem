<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_payment_management(): void
    {
        $this->get(route('payments.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_record_a_payment_and_view_it(): void
    {
        $user = User::factory()->create();
        $fee = $this->createFee(1000);

        $this->actingAs($user)
            ->get(route('payments.index'))
            ->assertOk()
            ->assertSee('Manage student fee payments and transactions.')
            ->assertSee(route('payments.create'), false);

        $this->get(route('payments.create'))
            ->assertOk()
            ->assertSee('Amina Otieno')
            ->assertSee('Balance: 1,000.00');

        $this->post(route('payments.store'), [
            'fee_id' => $fee->id,
            'amount' => '400.00',
            'method' => 'Bank',
            'transaction_reference' => 'BANK-123',
        ])->assertRedirect(route('payments.index'));

        $payment = Payment::firstOrFail();
        $this->assertDatabaseHas('payments', [
            'fee_id' => $fee->id,
            'amount' => '400.00',
            'method' => 'Bank',
            'transaction_reference' => 'BANK-123',
            'status' => 'Completed',
        ]);
        $this->assertSame('Partial', $fee->fresh()->status);

        $this->get(route('payments.index'))
            ->assertOk()
            ->assertSeeText('Amina Otieno')
            ->assertSeeText('Tuition')
            ->assertSeeText('Bank')
            ->assertSeeText('BANK-123')
            ->assertSeeText('400.00')
            ->assertSeeText('Completed');

        $this->post(route('payments.store'), [
            'fee_id' => $fee->id,
            'amount' => '600.00',
            'method' => 'Cash',
        ])->assertRedirect(route('payments.index'));

        $this->assertSame('Paid', $fee->fresh()->status);
        $this->assertDatabaseCount('payments', 2);
    }

    public function test_mpesa_payment_stores_provider_and_payer_details(): void
    {
        $fee = $this->createFee(2500);

        $this->actingAs(User::factory()->create())
            ->post(route('payments.store'), [
                'fee_id' => $fee->id,
                'amount' => '2500.00',
                'method' => 'M-Pesa',
                'transaction_reference' => 'QAB12XYZ',
                'phone_number' => '+254712345678',
            ])
            ->assertRedirect(route('payments.index'));

        $this->assertDatabaseHas('payments', [
            'fee_id' => $fee->id,
            'method' => 'M-Pesa',
            'transaction_reference' => 'QAB12XYZ',
            'provider' => 'Safaricom',
            'phone_number' => '+254712345678',
            'provider_transaction_id' => null,
            'status' => 'Completed',
        ]);
        $this->assertNotNull(Payment::firstOrFail()->paid_at);
    }

    public function test_payment_summary_displays_values_aggregated_from_payment_records(): void
    {
        $fee = $this->createFee(5000);
        Payment::create([
            'fee_id' => $fee->id,
            'amount' => '1200.00',
            'method' => 'Cash',
            'status' => 'Completed',
            'paid_at' => now(),
        ]);
        Payment::create([
            'fee_id' => $fee->id,
            'amount' => '300.00',
            'method' => 'M-Pesa',
            'status' => 'Pending',
        ]);
        Payment::create([
            'fee_id' => $fee->id,
            'amount' => '800.00',
            'method' => 'Bank',
            'status' => 'Completed',
            'paid_at' => now()->subMonth(),
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('payments.index'))
            ->assertOk()
            ->assertSee('KSh 2,000.00')
            ->assertSee('KSh 1,200.00')
            ->assertSee('KSh 300.00');
    }

    public function test_payment_list_filters_by_student_status_and_method(): void
    {
        $fee = $this->createFee(5000);
        Payment::create([
            'fee_id' => $fee->id,
            'amount' => '1200.00',
            'method' => 'Cash',
            'transaction_reference' => 'CASH-REF',
            'status' => 'Completed',
            'paid_at' => now(),
        ]);
        Payment::create([
            'fee_id' => $fee->id,
            'amount' => '300.00',
            'method' => 'M-Pesa',
            'transaction_reference' => 'MPESA-REF',
            'status' => 'Pending',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('payments.index', [
                'search' => 'Amina',
                'status' => 'Pending',
                'method' => 'M-Pesa',
            ]))
            ->assertOk()
            ->assertSee('MPESA-REF')
            ->assertDontSee('CASH-REF');
    }

    public function test_cheque_payment_records_its_reference(): void
    {
        $fee = $this->createFee(750);

        $this->actingAs(User::factory()->create())
            ->post(route('payments.store'), [
                'fee_id' => $fee->id,
                'amount' => '750.00',
                'method' => 'Cheque',
                'transaction_reference' => 'CHQ-1042',
            ])
            ->assertRedirect(route('payments.index'));

        $this->assertDatabaseHas('payments', [
            'fee_id' => $fee->id,
            'method' => 'Cheque',
            'transaction_reference' => 'CHQ-1042',
            'status' => 'Completed',
        ]);
    }

    public function test_payment_validation_prevents_overpayment_and_requires_method_details(): void
    {
        $fee = $this->createFee(500);
        $this->actingAs(User::factory()->create());

        $this->from(route('payments.create'))
            ->post(route('payments.store'), [
                'fee_id' => $fee->id,
                'amount' => '500.01',
                'method' => 'Cash',
            ])
            ->assertSessionHasErrors('amount');

        $this->from(route('payments.create'))
            ->post(route('payments.store'), [
                'fee_id' => $fee->id,
                'amount' => '100',
                'method' => 'M-Pesa',
            ])
            ->assertSessionHasErrors(['transaction_reference', 'phone_number']);

        $this->assertDatabaseCount('payments', 0);
        $this->assertSame('Pending', $fee->fresh()->status);
    }

    public function test_fully_paid_fees_are_not_available_for_another_payment(): void
    {
        $fee = $this->createFee(500, 'Paid');

        $this->actingAs(User::factory()->create())
            ->get(route('payments.create'))
            ->assertOk()
            ->assertSee('There are no outstanding fees to pay.')
            ->assertDontSee('Amina Otieno');
    }

    private function createFee(float $amount, string $status = 'Pending'): Fee
    {
        $course = Course::create(['name' => 'Computer Science']);
        $student = Student::create([
            'first_name' => 'Amina',
            'second_name' => 'Otieno',
            'course_id' => $course->id,
        ]);

        return Fee::create([
            'student_id' => $student->id,
            'amount' => $amount,
            'fee_type' => 'Tuition',
            'status' => $status,
        ]);
    }
}
