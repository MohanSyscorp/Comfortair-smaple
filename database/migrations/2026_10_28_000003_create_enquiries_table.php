<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile_number', 20);
            $table->string('email')->nullable();
            $table->string('service_required', 100);
            $table->string('product_interested_in')->nullable();
            $table->string('location', 255);
            $table->text('message')->nullable();
            $table->enum('status', [
                'New',
                'Contacted',
                'In Progress',
                'Completed',
                'Closed'
            ])->default('New');
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
