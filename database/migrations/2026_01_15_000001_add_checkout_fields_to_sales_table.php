<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('checkout_code')->nullable()->index()->after('transaction_date');
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['checkout_code', 'discount_percent']);
        });
    }
};
