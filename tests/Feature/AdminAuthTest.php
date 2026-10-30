<?php

namespace Tests\Feature;

use App\Models\ContactSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ContactSetting::create([
            'phone' => '+91 7708 650 535',
            'whatsapp' => '+91 7708 650 535',
            'email' => 'sales@itsk.in',
            'address' => 'Puducherry',
            'working_hours' => '9 AM - 8 PM',
            'google_maps_link' => 'https://maps.google.com',
        ]);
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin-login');
    }

    public function test_admin_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin-login');
        $response->assertStatus(200);
        $response->assertSee('Comfort Aircon');
        $response->assertSee('Sign In to Admin Panel');
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@comfortaircon.com',
            'password' => Hash::make('CorrectPassword123'),
            'status' => 'active',
        ]);

        $response = $this->post('/admin-login', [
            'email' => 'staff@comfortaircon.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_blocked_for_deactivated_accounts(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@comfortaircon.com',
            'password' => Hash::make('Password@123'),
            'status' => 'inactive',
        ]);

        $response = $this->post('/admin-login', [
            'email' => 'inactive@comfortaircon.com',
            'password' => 'Password@123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_active_super_admin_can_login_and_view_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@comfortaircon.com',
            'password' => Hash::make('Admin@12345'),
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $response = $this->post('/admin-login', [
            'email' => 'super@comfortaircon.com',
            'password' => 'Admin@12345',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);

        $dashboardResponse = $this->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Welcome back, Super Admin!');
        $dashboardResponse->assertSee('Roles & Permissions', false);
    }

    public function test_content_manager_cannot_see_users_in_sidebar(): void
    {
        $contentManager = User::factory()->create([
            'name' => 'Content Specialist',
            'email' => 'content@comfortaircon.com',
            'password' => Hash::make('Admin@12345'),
            'role' => 'content_manager',
            'permissions' => ['products', 'gallery', 'contact'],
            'status' => 'active',
        ]);

        $this->actingAs($contentManager);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Users & RBAC');
    }

    public function test_admin_can_logout_securely(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@comfortaircon.com',
            'password' => Hash::make('Admin@12345'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->post('/admin/logout');
        $response->assertRedirect('/admin-login');
        $this->assertGuest();
    }

    public function test_user_can_change_password_with_valid_current_password(): void
    {
        $user = User::factory()->create([
            'email' => 'staff2@comfortaircon.com',
            'password' => Hash::make('OldPassword123'),
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->post('/admin/password/change', [
            'current_password' => 'OldPassword123',
            'password' => 'NewSecurePassword123',
            'password_confirmation' => 'NewSecurePassword123',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123', $user->password));
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'email' => 'staff3@comfortaircon.com',
            'password' => Hash::make('OldPassword123'),
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->post('/admin/password/change', [
            'current_password' => 'WrongCurrentPassword',
            'password' => 'NewSecurePassword123',
            'password_confirmation' => 'NewSecurePassword123',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }
}
