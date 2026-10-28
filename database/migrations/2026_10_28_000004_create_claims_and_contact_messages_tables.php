<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('number');
            $table->string('code')->unique();
            $table->string('status');
            $table->string('document_type');
            $table->string('document_number', 20);
            $table->string('first_name');
            $table->string('paternal_surname');
            $table->string('maternal_surname');
            $table->string('phone');
            $table->string('email');
            $table->string('department');
            $table->string('province');
            $table->string('district');
            $table->string('address');
            $table->boolean('is_minor')->default(false);
            $table->string('guardian_name')->nullable();
            $table->string('guardian_document')->nullable();
            $table->string('good_type');
            $table->string('order_reference')->nullable();
            $table->decimal('claimed_amount', 10, 2);
            $table->text('good_description');
            $table->string('claim_type');
            $table->text('detail');
            $table->text('consumer_request');
            $table->timestamp('privacy_accepted_at');
            $table->boolean('email_notification_accepted')->default(false);
            $table->date('response_due_on');
            $table->text('provider_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['year', 'number']);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('subject');
            $table->text('message');
            $table->timestamp('privacy_accepted_at');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('claims');
    }
};
