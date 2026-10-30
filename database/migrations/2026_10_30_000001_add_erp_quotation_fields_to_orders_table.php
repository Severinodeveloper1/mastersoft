<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('erp_quotation_id')->nullable()->after('status');
            $table->string('erp_quotation_number')->nullable()->after('erp_quotation_id');
            $table->timestamp('erp_sent_at')->nullable()->after('erp_quotation_number');
            $table->text('erp_error')->nullable()->after('erp_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['erp_quotation_id', 'erp_quotation_number', 'erp_sent_at', 'erp_error']);
        });
    }
};
