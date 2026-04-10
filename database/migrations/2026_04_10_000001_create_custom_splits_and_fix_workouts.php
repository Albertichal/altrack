<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create custom_splits table (or patch if it already exists)
        if (!Schema::hasTable('custom_splits')) {
            Schema::create('custom_splits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('name', 50);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
                $table->unique(['user_id', 'name']);
            });
        } else {
            Schema::table('custom_splits', function (Blueprint $table) {
                if (!Schema::hasColumn('custom_splits', 'is_default')) {
                    $table->boolean('is_default')->default(false)->after('name');
                }
            });
        }

        // 2. Change workouts.split from ENUM to VARCHAR on MySQL
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE workouts MODIFY `split` VARCHAR(50) NOT NULL');
        }

        // 3. Seed default splits for all existing users
        $now     = now();
        $userIds = DB::table('users')->pluck('id');

        foreach ($userIds as $userId) {
            foreach (['PUSH', 'PULL', 'LEGS'] as $name) {
                DB::table('custom_splits')->insertOrIgnore([
                    'user_id'    => $userId,
                    'name'       => $name,
                    'is_default' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_splits');

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE workouts MODIFY `split` ENUM('PUSH','PULL','LEGS','FULL','UPPER','LOWER','CARDIO') NOT NULL");
        }
    }
};
