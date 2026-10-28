<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Mastersoft');
            $table->string('legal_name')->default('TROYA COMPANY E.I.R.L.');
            $table->string('ruc', 11)->default('20614007584');
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('claims_email')->nullable();
            $table->string('address')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('map_embed_url', 2048)->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->timestamps();
        });

        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->string('kicker')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedSmallInteger('years_of_experience')->nullable();
            $table->string('phone')->nullable();
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->text('values')->nullable();
            $table->timestamps();
        });

        Schema::create('legal_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_pages');
        Schema::dropIfExists('about_pages');
        Schema::dropIfExists('company_settings');
    }
};
