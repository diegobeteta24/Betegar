<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productables', function (Blueprint $table) {
            if (!Schema::hasColumn('productables', 'description')) {
                $table->string('description')->nullable()->after('subtotal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productables', function (Blueprint $table) {
            if (Schema::hasColumn('productables', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
