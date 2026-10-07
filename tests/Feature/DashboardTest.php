<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_the_dashboard_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_access_the_dashboard_without_verified_email(): void
    {
        $user = User::factory()->unverified()->create(['name' => 'Dashboard Student']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Dashboard Student')
            ->assertSeeText('Welcome to the Student Management System.')
            ->assertSeeText('Total Students')
            ->assertSeeText('250')
            ->assertSeeText('Total Courses')
            ->assertSeeText('12')
            ->assertSeeText('Fees Collected')
            ->assertSeeText('KES 500,000')
            ->assertSeeText('Outstanding Fees')
            ->assertSeeText('KES 120,000')
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
