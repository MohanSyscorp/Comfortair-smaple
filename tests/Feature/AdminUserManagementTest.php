<?php

namespace Tests\Feature;

use App\Models\ContactSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $contentManager;

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

        $this->superAdmin = User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'super@comfortaircon.com',
            'role' => 'super_admin',
            'status' => 'active',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Operations Admin',
            'email' => 'admin@comfortaircon.com',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->contentManager = User::factory()->create([
            'name' => 'Content Specialist',
            'email' => 'content@comfortaircon.com',
            'role' => 'content_manager',
            'status' => 'active',
        ]);
    }

    public function test_guest_is_redirected_from_users_index(): void
    {
        $response = $this->get('/admin/users');
        $response->assertRedirect('/admin-login');
    }

    public function test_regular_admin_is_forbidden_from_users_module(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_content_manager_is_forbidden_from_users_module(): void
    {
        $this->actingAs($this->contentManager);

        $response = $this->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_users_list(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('Roles & Permissions', false);
        $response->assertSee('Super Administrator');
        $response->assertSee('Operations Admin');
        $response->assertSee('Content Specialist');
    }

    public function test_super_admin_can_create_new_staff_member(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post('/admin/users', [
            'name' => 'Priya Raman',
            'email' => 'priya@comfortaircon.com',
            'password' => 'StaffPassword123',
            'role' => 'admin',
            'permissions' => ['products', 'gallery', 'enquiries', 'contact'],
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Priya Raman',
            'email' => 'priya@comfortaircon.com',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    public function test_super_admin_can_update_staff_member(): void
    {
        $this->actingAs($this->superAdmin);

        $staff = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'staff_update@comfortaircon.com',
            'role' => 'content_manager',
            'status' => 'active',
        ]);

        $response = $this->put("/admin/users/{$staff->id}", [
            'name' => 'Updated Staff Name',
            'email' => 'staff_update@comfortaircon.com',
            'role' => 'content_manager',
            'permissions' => ['products', 'gallery', 'contact'],
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/users');
        $staff->refresh();
        $this->assertEquals('Updated Staff Name', $staff->name);
    }

    public function test_super_admin_can_toggle_user_status(): void
    {
        $this->actingAs($this->superAdmin);

        $staff = User::factory()->create([
            'status' => 'active',
        ]);

        $response = $this->patch("/admin/users/{$staff->id}/status");
        $response->assertRedirect();

        $staff->refresh();
        $this->assertEquals('inactive', $staff->status);

        // Toggle back to active
        $this->patch("/admin/users/{$staff->id}/status");
        $staff->refresh();
        $this->assertEquals('active', $staff->status);
    }

    public function test_super_admin_cannot_deactivate_themselves(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->patch("/admin/users/{$this->superAdmin->id}/status");
        $response->assertSessionHasErrors('error');

        $this->superAdmin->refresh();
        $this->assertEquals('active', $this->superAdmin->status);
    }

    public function test_super_admin_can_delete_staff_member(): void
    {
        $this->actingAs($this->superAdmin);

        $staff = User::factory()->create();

        $response = $this->delete("/admin/users/{$staff->id}");
        $response->assertRedirect('/admin/users');

        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    public function test_super_admin_cannot_delete_themselves(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->delete("/admin/users/{$this->superAdmin->id}");
        $response->assertSessionHasErrors('error');

        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }
}
