<?php

namespace Tests\Feature;

use App\Mail\EnquiryReceivedMail;
use App\Models\ContactSetting;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminEnquiryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $contentManager;
    protected Enquiry $enquiry;

    protected function setUp(): void
    {
        parent::setUp();

        ContactSetting::create([
            'phone' => '+91 7708 650 535',
            'whatsapp' => '+91 7708 650 535',
            'email' => 'sales@itsk.in',
            'address' => 'No.37, Kamaraj Salai, Thattanchavady, Puducherry - 605009',
            'working_hours' => '9 AM - 8 PM',
            'google_maps_link' => 'https://maps.google.com',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['products', 'gallery', 'enquiries', 'contact'],
            'status' => 'active',
        ]);

        $this->contentManager = User::factory()->create([
            'role' => 'content_manager',
            'permissions' => ['products', 'gallery', 'contact'],
            'status' => 'active',
        ]);

        $this->enquiry = Enquiry::create([
            'name' => 'Karthik Subramanian',
            'mobile_number' => '9876543210',
            'email' => 'karthik@example.com',
            'service_required' => 'AC Installation',
            'product_interested_in' => 'Carrier Ester Neo 1.5 Ton',
            'location' => 'Pondicherry',
            'message' => 'Need 2 split units installed in Lawspet apartment.',
            'status' => 'New',
        ]);
    }

    public function test_guest_is_redirected_from_enquiries(): void
    {
        $response = $this->get('/admin/enquiries');
        $response->assertRedirect('/admin-login');
    }

    public function test_content_manager_is_forbidden_from_enquiries(): void
    {
        $this->actingAs($this->contentManager);

        $response = $this->get('/admin/enquiries');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_enquiries_pipeline(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/enquiries');
        $response->assertStatus(200);
        $response->assertSee('Enquiry Management', false);
        $response->assertSee('Karthik Subramanian');
        $response->assertSee('9876543210');
        $response->assertSee('AC Installation');
    }

    public function test_admin_can_view_enquiry_details_json(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get("/admin/enquiries/{$this->enquiry->id}");
        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'Karthik Subramanian',
            'mobile_number' => '9876543210',
            'email' => 'karthik@example.com',
            'service_required' => 'AC Installation',
            'location' => 'Pondicherry',
            'status' => 'New',
        ]);
    }

    public function test_admin_can_progress_lead_status(): void
    {
        $this->actingAs($this->admin);

        // Progress from New to Contacted
        $response = $this->patch("/admin/enquiries/{$this->enquiry->id}/status", [
            'status' => 'Contacted',
        ]);
        $response->assertRedirect();
        $this->enquiry->refresh();
        $this->assertEquals('Contacted', $this->enquiry->status);

        // Progress to In Progress
        $this->patch("/admin/enquiries/{$this->enquiry->id}/status", ['status' => 'In Progress']);
        $this->enquiry->refresh();
        $this->assertEquals('In Progress', $this->enquiry->status);

        // Progress to Completed
        $this->patch("/admin/enquiries/{$this->enquiry->id}/status", ['status' => 'Completed']);
        $this->enquiry->refresh();
        $this->assertEquals('Completed', $this->enquiry->status);
    }

    public function test_lead_status_rejects_invalid_values(): void
    {
        $this->actingAs($this->admin);

        $response = $this->patch("/admin/enquiries/{$this->enquiry->id}/status", [
            'status' => 'InvalidStatus',
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_admin_can_delete_enquiry(): void
    {
        $this->actingAs($this->admin);

        $response = $this->delete("/admin/enquiries/{$this->enquiry->id}");
        $response->assertRedirect('/admin/enquiries');

        $this->assertDatabaseMissing('enquiries', ['id' => $this->enquiry->id]);
    }

    public function test_enquiry_notification_mail_renders_content_correctly(): void
    {
        $mailable = new EnquiryReceivedMail($this->enquiry);

        $mailable->assertHasSubject("New Customer Enquiry: AC Installation — Karthik Subramanian");
        $mailable->assertSeeInHtml('Karthik Subramanian');
        $mailable->assertSeeInHtml('9876543210');
        $mailable->assertSeeInHtml('AC Installation');
        $mailable->assertSeeInHtml('Need 2 split units installed in Lawspet apartment.');
    }
}
