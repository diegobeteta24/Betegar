<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_order_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('work_order_entries', 'work_date')) {
                $table->date('work_date')->after('work_order_id')->index();
            }
            if (!Schema::hasColumn('work_order_entries', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('requests');
            }
            if (!Schema::hasColumn('work_order_entries', 'signature_by')) {
                $table->string('signature_by')->nullable()->after('signed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_order_entries', function (Blueprint $table) {
            if (Schema::hasColumn('work_order_entries', 'signature_by')) {
                $table->dropColumn('signature_by');
            }
            if (Schema::hasColumn('work_order_entries', 'signed_at')) {
                $table->dropColumn('signed_at');
            }
            if (Schema::hasColumn('work_order_entries', 'work_date')) {
                $table->dropIndex(['work_date']);
                $table->dropColumn('work_date');
            }
        });
    }
};
