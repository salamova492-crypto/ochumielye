<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creativity_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('leader_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->date('date');
            $table->enum('time_slot', ['09:00-11:00', '11:00-13:00', '13:00-15:00', '15:00-17:00']);
            $table->integer('maxPeople');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['date', 'time_slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_classes');
    }
};
