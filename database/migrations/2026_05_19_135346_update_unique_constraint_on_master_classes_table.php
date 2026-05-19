<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_classes', function (Blueprint $table) {
            $table->dropUnique(['date', 'time_slot']);
            $table->unique(['leader_id', 'date', 'time_slot']);
        });
    }

    public function down(): void
    {
        Schema::table('master_classes', function (Blueprint $table) {
            $table->dropUnique(['leader_id', 'date', 'time_slot']);
            $table->unique(['date', 'time_slot']);
        });
    }
};
