<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('krathongs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);           // ชนิดกระทง
            $table->string('nickname', 50);
            $table->unsignedTinyInteger('age');
            $table->string('wish', 200);
            $table->ipAddress('ip')->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('type');
        });
    }
    public function down(): void {
        Schema::dropIfExists('krathongs');
    }
};
