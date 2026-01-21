<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'image_path')) {
                if (Schema::hasColumn('categories', 'description')) {
                    $table->string('image_path')->nullable()->after('description');
                } else {
                    $table->string('image_path')->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
