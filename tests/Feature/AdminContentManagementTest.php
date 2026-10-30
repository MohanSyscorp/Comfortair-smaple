<?php

namespace Tests\Feature;

use App\Models\ContactSetting;
use App\Models\GalleryItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $contentManager;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        ContactSetting::create([
            'phone' => '+91 7708 650 535',
            'whatsapp' => '+91 7708 650 535',
            'email' => 'sales@itsk.in',
            'address' => 'Puducherry',
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
    }

    // ----------------------------------------------------
    // Product Management Tests
    // ----------------------------------------------------
    public function test_can_view_products_index(): void
    {
        $this->actingAs($this->contentManager);

        $response = $this->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('Product Management');
    }

    public function test_can_create_carrier_product_with_image(): void
    {
        $this->actingAs($this->admin);

        $fakeImage = UploadedFile::fake()->image('carrier-ester.jpg', 600, 600);

        $response = $this->post('/admin/products', [
            'name' => 'Carrier Ester Neo 1.5 Ton',
            'model' => 'CAI18ER3R30F0',
            'specifications' => "Capacity: 1.5 Ton\n3 Star Rating",
            'features' => "PM 2.5 Dual Filter\nFast Cool Turbo",
            'image' => $fakeImage,
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', [
            'name' => 'Carrier Ester Neo 1.5 Ton',
            'model' => 'CAI18ER3R30F0',
            'status' => 'published',
        ]);

        $product = Product::where('model', 'CAI18ER3R30F0')->first();
        $this->assertNotNull($product);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_can_toggle_product_publish_status(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'Test Model',
            'model' => 'TEST-001',
            'specifications' => 'Specs',
            'features' => 'Features',
            'image' => 'products/sample.jpg',
            'status' => 'published',
        ]);

        $response = $this->patch("/admin/products/{$product->id}/publish");
        $response->assertRedirect();

        $product->refresh();
        $this->assertEquals('unpublished', $product->status);

        // Toggle back to published
        $this->patch("/admin/products/{$product->id}/publish");
        $product->refresh();
        $this->assertEquals('published', $product->status);
    }

    public function test_can_delete_product(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'name' => 'Delete Model',
            'model' => 'DEL-001',
            'specifications' => 'Specs',
            'features' => 'Features',
            'image' => 'products/del.jpg',
            'status' => 'unpublished',
        ]);

        $response = $this->delete("/admin/products/{$product->id}");
        $response->assertRedirect('/admin/products');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    // ----------------------------------------------------
    // Gallery Management Tests
    // ----------------------------------------------------
    public function test_can_view_gallery_index(): void
    {
        $this->actingAs($this->contentManager);

        $response = $this->get('/admin/gallery');
        $response->assertStatus(200);
        $response->assertSee('Gallery Management');
    }

    public function test_can_upload_gallery_item_with_approved_category(): void
    {
        $this->actingAs($this->contentManager);

        $fakePhoto = UploadedFile::fake()->image('installation-site.jpg', 800, 600);

        $response = $this->post('/admin/gallery', [
            'image' => $fakePhoto,
            'caption' => 'Lawspet Inverter Installation',
            'category' => 'Installation photos',
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/gallery');
        $this->assertDatabaseHas('gallery_items', [
            'caption' => 'Lawspet Inverter Installation',
            'category' => 'Installation photos',
            'status' => 'published',
        ]);
    }

    public function test_gallery_upload_rejects_unapproved_category(): void
    {
        $this->actingAs($this->contentManager);

        $fakePhoto = UploadedFile::fake()->image('random.jpg');

        $response = $this->post('/admin/gallery', [
            'image' => $fakePhoto,
            'caption' => 'Random',
            'category' => 'Unapproved Category',
            'status' => 'published',
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_can_toggle_gallery_publish_status(): void
    {
        $this->actingAs($this->admin);

        $item = GalleryItem::create([
            'image' => 'gallery/test.jpg',
            'caption' => 'Test Caption',
            'category' => 'Completed projects',
            'status' => 'published',
        ]);

        $response = $this->patch("/admin/gallery/{$item->id}/publish");
        $response->assertRedirect();

        $item->refresh();
        $this->assertEquals('unpublished', $item->status);
    }

    public function test_can_delete_gallery_item(): void
    {
        $this->actingAs($this->admin);

        $item = GalleryItem::create([
            'image' => 'gallery/delete-test.jpg',
            'caption' => 'To be deleted',
            'category' => 'Installation photos',
            'status' => 'unpublished',
        ]);

        $response = $this->delete("/admin/gallery/{$item->id}");
        $response->assertRedirect('/admin/gallery');

        $this->assertDatabaseMissing('gallery_items', ['id' => $item->id]);
    }

    // ----------------------------------------------------
    // Contact Management Tests
    // ----------------------------------------------------
    public function test_can_view_contact_settings_form(): void
    {
        $this->actingAs($this->contentManager);

        ContactSetting::create([
            'phone' => '+91 7708 650 535',
            'whatsapp' => '+91 7708 650 535',
            'email' => 'sales@comfortaircon.com',
            'address' => 'No.37, Kamaraj Salai, Puducherry - 605009',
            'working_hours' => 'Mon - Sat: 9:00 AM - 8:00 PM',
            'google_maps_link' => 'https://maps.google.com/?q=Puducherry',
        ]);

        $response = $this->get('/admin/contact');
        $response->assertStatus(200);
        $response->assertSee('Edit Contact Details');
    }

    public function test_can_update_contact_settings(): void
    {
        $this->actingAs($this->admin);

        ContactSetting::create([
            'phone' => '+91 7708 650 535',
            'whatsapp' => '+91 7708 650 535',
            'email' => 'sales@comfortaircon.com',
            'address' => 'No.37, Kamaraj Salai, Puducherry - 605009',
            'working_hours' => 'Mon - Sat: 9:00 AM - 8:00 PM',
            'google_maps_link' => 'https://maps.google.com/?q=Puducherry',
        ]);

        $response = $this->post('/admin/contact', [
            'phone' => '+91 9876 543 210',
            'whatsapp' => '+91 9876 543 210',
            'email' => 'contact@comfortaircon.com',
            'address' => 'No.100, MG Road, Puducherry - 605001',
            'working_hours' => 'Mon - Sun: 8:00 AM - 9:00 PM',
            'google_maps_link' => 'https://maps.google.com/?q=Pondicherry',
            'facebook_url' => 'https://facebook.com/newcomfort',
            'instagram_url' => 'https://instagram.com/newcomfort',
            'twitter_url' => null,
            'youtube_url' => null,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_settings', [
            'phone' => '+91 9876 543 210',
            'email' => 'contact@comfortaircon.com',
            'facebook_url' => 'https://facebook.com/newcomfort',
            'twitter_url' => null,
        ]);
    }
}
