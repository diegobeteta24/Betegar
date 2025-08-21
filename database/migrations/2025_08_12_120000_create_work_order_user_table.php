<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_order_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['work_order_id','user_id']);
        });

        // Backfill: copy existing user_id assignments into the pivot
        if (Schema::hasTable('work_orders')) {
            $rows = \DB::table('work_orders')->select('id','user_id')->whereNotNull('user_id')->get();
            foreach ($rows as $r) {
                \DB::table('work_order_user')->updateOrInsert([
                    'work_order_id' => $r->id,
                    'user_id' => $r->user_id,
                ], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_user');
    }
};
