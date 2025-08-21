<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bank_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_transactions','origin_type')) {
                $table->string('origin_type')->nullable()->index()->after('description');
            }
            if (!Schema::hasColumn('bank_transactions','origin_id')) {
                $table->unsignedBigInteger('origin_id')->nullable()->index()->after('origin_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('bank_transactions','origin_type')) {
                $table->dropColumn('origin_type');
            }
            if (Schema::hasColumn('bank_transactions','origin_id')) {
                $table->dropColumn('origin_id');
            }
        });
    }
};
