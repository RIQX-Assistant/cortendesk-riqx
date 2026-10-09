<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RIQX: ads shown in the RIQX app — ADS1 (bottom of the left column) and ADS2
 * (banner above the status bar; also the Android bottom banner).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riqx_ads', function (Blueprint $table) {
            $table->id();
            $table->string('slot', 8);                       // ads1 | ads2
            $table->string('title', 120);
            $table->string('media_type', 8);                 // image | gif | video
            $table->string('filename');                      // on the riqx_ads disk
            $table->string('original_name');
            $table->string('mime', 64);
            $table->unsignedBigInteger('size');
            $table->char('sha256', 64);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration_seconds')->default(8); // images; videos play to the end
            $table->string('link_url', 500)->nullable();
            $table->string('locale', 4)->default('all');     // all | fa | en
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['slot', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riqx_ads');
    }
};
