<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(
                "ALTER TABLE workouts CHANGE `name` `split` ENUM('PUSH','PULL','LEGS','FULL','UPPER','LOWER','CARDIO') NOT NULL"
            );
        } else {
            Schema::table('workouts', function (Blueprint $table) {
                $table->renameColumn('name', 'split');
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE workouts CHANGE `split` `name` VARCHAR(255) NOT NULL');
        } else {
            Schema::table('workouts', function (Blueprint $table) {
                $table->renameColumn('split', 'name');
            });
        }
    }
};
