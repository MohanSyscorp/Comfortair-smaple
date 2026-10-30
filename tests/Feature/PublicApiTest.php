<?php

namespace Tests\Feature;

use App\Mail\EnquiryReceivedMail;
use App\Models\ContactSetting;
use App\Models\GalleryItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ContactSetting::create([
            'phone' => '+91 7708 650 535',
            'whatsapp' => '+91 7708 650 535',
            'email' => 'sales@itsk.in',
            'address' => 'No.37, Kamaraj Salai, Thattanchavady, Puducherry - 605009',
            'working_hours' => 'Mon - Sat: 9:00 AM - 8:00 PM',
            'google_maps_link' => 'https://maps.google.com/?q=Puducherry',
            'facebook_url' => 'https://facebook.com/comfortaircon',
            'instagram_url' => 'https://instagram.com/comfortaircon',
            'twitter_url' => null,
            'youtube_url' => null,
        ]);
    }

    public function test_get_contact_returns_synchronized_details(): void
    {
        $response = $this->getJson('/api/v1/contact');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'phone' => '+91 7708 650 535',
                    'whatsapp' => '+91 7708 650 535',
                    'email' => 'sales@itsk.in',
                    'address' => 'No.37, Kamaraj Salai, Thattanchavady, Puducherry - 605009',
                    'working_hours' => 'Mon - Sat: 9:00 AM - 8:00 PM',
                    'facebook_url' => 'https://facebook.com/comfortaircon',
                    'twitter_url' => null,
                ],
            ]);
    }

    public function test_get_products_returns_only_published_items_and_no_ecommerce_fields(): void
    {
        $published = Product::create([
            'name' => 'Carrier Ester Neo 1.5 Ton Split AC',
            'model' => 'CAI18ER3R30F0',
            'specifications' => '1.5 Ton 3 Star',
            'features' => 'PM 2.5 Dual Filter',
            'image' => 'products/ester.jpg',
            'status' => 'published',
        ]);

        $unpublished = Product::create([
            'name' => 'Carrier Draft Model',
            'model' => 'DRAFT-001',
            'specifications' => 'Draft Specs',
            'features' => 'Draft Features',
            'image' => 'products/draft.jpg',
            'status' => 'unpublished',
        ]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 1,
            ])
            ->assertJsonFragment([
                'name' => 'Carrier Ester Neo 1.5 Ton Split AC',
                'model' => 'CAI18ER3R30F0',
            ])
            ->assertJsonMissing([
                'name' => 'Carrier Draft Model',
            ]);

        // Verify strict exclusion of commercial/ecommerce attributes (Section 7.4)
        $data = $response->json('data.0');
        $this->assertArrayNotHasKey('price', $data);
        $this->assertArrayNotHasKey('stock', $data);
        $this->assertArrayNotHasKey('cart', $data);
        $this->assertArrayNotHasKey('reviews', $data);
    }

    public function test_get_gallery_returns_published_items_with_category_filter(): void
    {
        GalleryItem::create([
            'image' => 'gallery/install1.jpg',
            'caption' => 'Lawspet Site',
            'category' => 'Installation photos',
            'status' => 'published',
        ]);

        GalleryItem::create([
            'image' => 'gallery/service1.jpg',
            'caption' => 'Jet Cleaning',
            'category' => 'Service work',
            'status' => 'published',
        ]);

        GalleryItem::create([
            'image' => 'gallery/draft1.jpg',
            'caption' => 'Unpublished Photo',
            'category' => 'Installation photos',
            'status' => 'unpublished',
        ]);

        // 1. Fetch all published
        $allResponse = $this->getJson('/api/v1/gallery');
        $allResponse->assertStatus(200)
            ->assertJson(['success' => true, 'count' => 2])
            ->assertJsonMissing(['caption' => 'Unpublished Photo']);

        // 2. Fetch filtered by category
        $filteredResponse = $this->getJson('/api/v1/gallery?category=Installation photos');
        $filteredResponse->assertStatus(200)
            ->assertJson(['success' => true, 'count' => 1])
            ->assertJsonFragment(['caption' => 'Lawspet Site'])
            ->assertJsonMissing(['caption' => 'Jet Cleaning']);
    }

    public function test_submit_enquiry_persists_lead_and_triggers_email(): void
    {
        Mail::fake();

        $payload = [
            'name' => 'Subramanian R',
            'mobile_number' => '9840123456',
            'email' => 'subbu@gmail.com',
            'service_required' => 'AC Installation',
            'product_interested_in' => 'Carrier Ester Neo 1.5 Ton Split AC',
            'location' => 'Pondicherry',
            'message' => 'Please provide quote for 2 units in White Town.',
        ];

        $response = $this->postJson('/api/v1/enquiry', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Thank you! Your enquiry has been received. Our team will contact you shortly.',
            ]);

        $this->assertDatabaseHas('enquiries', [
            'name' => 'Subramanian R',
            'mobile_number' => '9840123456',
            'email' => 'subbu@gmail.com',
            'service_required' => 'AC Installation',
            'location' => 'Pondicherry',
            'status' => 'New',
        ]);

        // Assert notification email was sent to business mailbox
        Mail::assertSent(EnquiryReceivedMail::class, function ($mail) {
            return $mail->hasTo('sales@itsk.in');
        });
    }

    public function test_submit_enquiry_validates_10_digit_indian_mobile(): void
    {
        Mail::fake();

        // 1. Invalid mobile number (too short)
        $response = $this->postJson('/api/v1/enquiry', [
            'name' => 'Subramanian R',
            'mobile_number' => '12345',
            'service_required' => 'Repair',
            'location' => 'Pondicherry',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['mobile_number']);

        Mail::assertNothingSent();
    }

    public function test_submit_enquiry_enforces_500_char_message_limit(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/enquiry', [
            'name' => 'Subramanian R',
            'mobile_number' => '9840123456',
            'service_required' => 'Repair',
            'location' => 'Pondicherry',
            'message' => str_repeat('A', 501),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }
}
